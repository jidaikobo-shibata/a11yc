<?php
/**
 * A11yc\Guzzle
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Guzzle
{
	protected static $_instances = array();
	protected $cons = array();
	protected $errors = array();
	protected $cons_tmp = array();
	protected $url;
	protected $real_url;
	protected $headers;
	protected $status_code;
	protected $body;
	protected $is_exists;
	protected $is_html;
	protected $is_xml;
	protected $is_pdf;
	protected $is_other; // CSS, js, images, Word, Excel...

	/**
	 * _init
	 *
	 * @return  void
	 */
	public static function _init()
	{
		require (A11YC_LIB_PATH.'/guzzle/vendor/autoload.php');
	}

	/**
	 * instance
	 *
	 * @param   string    $url
	 * @return  instance
	 */
	public static function instance($url)
	{
		return array_key_exists($url, static::$_instances) ? static::$_instances[$url] : FALSE;
	}

	/**
	 * Create Crawler object
	 *
	 * @param   string $url  Identifier for this request
	 * @param   array  $cons configuration
	 * @return  void
	 */
	public static function forge($url, $cons = array())
	{
		// exists
		if ( ! static::instance($url))
		{
			// instance
			static::$_instances[$url] = new static($url, $cons);

			// set basic auth from setup
			$setup = Controller_Setup::fetch_setup();
			$basic_user = Arr::get($setup, 'basic_user');
			$basic_pass = Arr::get($setup, 'basic_pass');
			if ($basic_user && $basic_pass)
			{
				static::instance($url)->set_config('auth', array($basic_user, $basic_pass));
			}
		}
	}

	/**
	 * __construct
	 * simply try to access and store status code.
	 * HEAD request is little bit faster than GET request.
	 *
	 * @param   string $url  Identifier for this request
	 * @param   array  $cons configuration
	 * @return  void
	 */
	public function __construct($url, $cons = array())
	{
		$this->url = $url;
		$this->cons = $cons;
	}

	/**
	 * simple HEAD request
	 * to use broken link check. This method won't return real_url.
	 *
	 * @return  bool
	 */
	private function head()
	{
		// don't call twice
		if (isset($this->status_code) && $this->status_code) return true;
		$url = $this->url;

		// override temporary config
		$cons = $this->get_config();

		$client = new \GuzzleHttp\Client(array(
				'http_errors' => false,
			));

		// client
		try {
			try {
				$response = $client->head($url, $cons);
			} catch (\GuzzleHttp\Exception\ConnectException $e) {
				$this->errors[] = 'ConnectException';
				return null;
			}
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			$this->errors[] = 'RequestException';
			return null;
		}

		// set values
		$this->status_code = $response->getStatusCode();
		$this->headers     = $response->getHeaders();
		$this->is_exists   = ($this->status_code == 200);
		$this->is_html     = $this->is_html($response);
		return true;
	}

	/**
	 * GET request
	 * to use fetch complex values such as HTML, real urls.
	 *
	 * @return  mixed
	 */
	private function get()
	{
		// don't call twice
		if (isset($this->body) && $this->body) return true;
		$url = $this->url;

		// override temporary config
		$cons = $this->get_config();

		// handler
		$stack = \GuzzleHttp\HandlerStack::create();
		$lastRequest = null;
		$stack->push(\GuzzleHttp\Middleware::mapRequest(
				function (\Psr\Http\Message\RequestInterface $request) use(&$lastRequest) {
					$lastRequest = $request;
					return $request;
				}));

		// client
		$client = new \GuzzleHttp\Client(array(
				'http_errors' => false,
				'handler' => $stack,
				\GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true
			));

		// config need to be used twice. is this something wrong ?-(
		$request = new \GuzzleHttp\Psr7\Request('GET', $url, $cons);

		// basic-auth user and passwd is need to be set here.
		try {
			try {
			$response = $client->send($request, $cons);
			} catch (\GuzzleHttp\Exception\ConnectException $e) {
				$this->errors[] = 'ConnectException';
				return false;
			}
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			$this->errors[] = 'RequestException';
			return false;
		}

		// set values
		$this->real_url = $lastRequest->getUri()->__toString();
		$this->body     = $this->encoding($response->getBody()->getContents());

		// over write if already exists
		$this->status_code = $response->getStatusCode();
		$this->headers     = $response->getHeaders();
		$this->is_exists   = ($this->status_code == 200);
		$this->is_html     = $this->is_html($response);
		return true;
	}

	/**
	 * text encoding
	 *
	 * @param   object $response
	 * @return  void
	 */
	private function encoding($body)
	{
		// trust declaration
		if (strpos($body, 'charset="UTF-8"') !== false)
		{
			return $body;
		}

		// try to detect
		$encodes = array("ASCII", "SJIS-win", "SJIS", "ISO-2022-JP", "EUC-JP");
		$encode = mb_detect_encoding($body, array_merge($encodes, array("UTF-8")));

		if (in_array($encode, $encodes))
		{
			$body = mb_convert_encoding($body, "UTF-8", $encode);
		}
		return $body;
	}

	/**
	 * is html.
	 *
	 * @param   object $response
	 * @return  void
	 */
	private function is_html($response)
	{
		return strpos($response->getHeaderLine('Content-Type'), 'html') !== false;
	}

	/**
	 * set config
	 *
	 * @param   string $name
	 * @param   mixed  $value
	 * @return  void
	 */
	public function set_config($name, $val)
	{
		$this->cons = array_merge($this->cons, array($name => $val));

		// at setting config, clear stored data
		$this->status_code = '';
		$this->body = '';
	}

	/**
	 * get config
	 *
	 * @return  array
	 */
	private function get_config()
	{
		return $this->cons;
	}

	/**
	 * Fetch a values
	 *
	 * @param   string
	 * @return  mixed
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			// errors?
			if ($name == 'errors')
			{
				return $this->errors;
			}
			// get status code at the first time. simple requests.
			elseif (in_array($name, array('status_code', 'headers', 'is_exists', 'is_html')))
			{
				if ( ! $this->head())
				{
					return null;
				}
			}
			// need GET requests.
			else
			{
				if ( ! $this->get())
				{
					return null;
				}
			}
			return $this->$name;
		}
	}
}
