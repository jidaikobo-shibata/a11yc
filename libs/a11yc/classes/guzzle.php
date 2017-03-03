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
	 * @return  void
	 */
	private function head()
	{
		// don't call twice
		if ( ! $this->cons_tmp && isset($this->status_code)) return;
		$url = $this->url;

		// override temporary config
		$cons = $this->get_config();

		// client
		$client = new \GuzzleHttp\Client(array(
				'http_errors' => false,
			));
		$response = $client->head($url, $cons);

		// set values
		$this->status_code = $response->getStatusCode();
		$this->headers     = $response->getHeaders();
		$this->is_exists   = ($this->status_code == 200);
		$this->is_html     = $this->is_html($response);
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
		if ( ! $this->cons_tmp && isset($this->body)) return;
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
		$response = $client->send($request, $cons);

		// set values
		$this->real_url = $lastRequest->getUri()->__toString();
		$this->body     = $response->getBody()->getContents();

		// over write if already exists
		$this->status_code = $response->getStatusCode();
		$this->headers     = $response->getHeaders();
		$this->is_exists   = ($this->status_code == 200);
		$this->is_html     = $this->is_html($response);
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
		$this->cons_tmp = array_merge($this->cons_tmp, array($name => $val));
	}

	/**
	 * get config
	 *
	 * @return  array
	 */
	private function get_config()
	{
		$cons = $this->cons_tmp ?: $this->cons;
		$this->cons = $cons;
		$this->cons_tmp = array();
		return $cons;
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
			// get status code at the first time. simple requests.
			if (in_array($name, array('status_code', 'headers', 'is_exists', 'is_html')))
			{
				$this->head();
			}
			// need GET requests.
			else
			{
				$this->get();
			}
			return $this->$name;
		}
	}
}
