<?php
/**
 * A11yc\Crawl
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Crawl
{
	protected static $target_path;

	/**
	 * set_target_path
	 *
	 * param string $target_path
	 * @return  void
	 */
	public static function set_target_path($target_path)
	{
		static::$target_path = static::remove_index($target_path, '/');
	}

	/**
	 * is valid scheme
	 *
	 * param string $url
	 * @return  bool
	 */
	public static function is_valid_scheme($url)
	{
		$url = Util::urldec($url);

		// scheme
		$scheme = substr($url, 0, strpos($url, ':'));

		// do not contain "mailto" because mailto doesn't return header.
		$schemes = array(
			'http', 'https', 'file', 'ftp', 'gopher', 'news',
			'nntp', 'telnet', 'wais', 'prospero'
		);

		return in_array($scheme, $schemes);
	}

	/**
	 * remove_index
	 *
	 * param string $url
	 * @return  string
	 */
	public static function remove_index($url)
	{
		$url = trim($url);
		$url = rtrim($url, '/');
		$url = Util::urldec($url);
		$url  = str_replace(
			array('/index.htm', '/index.html', '/index.php'),
			'',
			$url);
		return $url;
	}

	/**
	 * upper_path
	 *
	 * param string $str
	 * @return  string
	 */
	public static function upper_path($str)
	{
		if (empty(static::$target_path)) Util::error('set set_target_path()');

		// count ups
		$num = substr_count($str, '../');
		$ret = '';
		for ($n = 1; $n <= $num; $n++)
		{
			$ret = dirname(static::$target_path);
		}

		// is page exists?
		$headers = @get_headers($ret);
		if (
			$headers !== false &&
			(
				strpos($headers[0], ' 20') !== false ||
				strpos($headers[0], ' 30') !== false
			)
		)
		{
			return $ret;
		}

		return static::$target_path;
	}

	/**
	 * keep url unique
	 *
	 * @param   strings  $str ['http://...', '/', '../', '/..']
	 * @return  strings
	 */
	public static function keep_url_unique($str)
	{
		// target path
		if (empty(static::$target_path)) Util::error('set set_target_path()');

		// root path
		$root_path = join("/", array_slice(explode("/", static::$target_path), 0, 3));

		// empty
		if (empty($str)) return static::$target_path;

		// keep it
		if (
			substr($str, 0, 2) == '//' || // care with start with '//'
			static::is_valid_scheme($str) // scheme
			// (strpos($str, ':') !== false && ! in_array($scheme, $schemes))
		)
		{
			$str = $str;
		}
		// absolute root path
		else if ($str == '/')
		{
			$str = $root_path;
		}
		// fragment
		else if ($str[0] == '#')
		{
			$str = static::$target_path.'/'.$str;
		}
		// root relative path. eg. '/foo...' to 'http://example.com/foo..'
		else if ($str[0] == '/' && isset($str[1]) && $str[1] != '/')
		{
			$str = $root_path.$str;
		}
		// relative current path. eg. './foo...' to 'http://example.com/path/to/foo..'
		else if(substr($str, 0, 2) == './')
		{
			$str = static::$target_path.'/'.substr($str, 2);
		}
		// relative upper path. eg. '../foo...' to 'http://example.com/path/to/foo..'
		else if(substr($str, 0, 3) == '../')
		{
			$str = static::upper_path($str).'/'.substr($str, 3);
		}
		// maybe link to file
		else
		{
			$str = static::$target_path.'/'.$str;
		}

		// remove tailing slash
		$str = $str != '/' ? rtrim($str, '/') : $str;

		return $str;
	}


	/**
	 * get_host_from_url
	 *
	 * @param   string     $url
	 * @return  string|bool
	 */
	public static function get_host_from_url($url)
	{
		// cannot get host
		if (substr($url, 0, 4) != 'http')
		{
			return false;
		}
		// plural /
		else if (substr_count($url, '/') >= 3)
		{
			$hosts = explode('/', $url);
			return $hosts[0].$hosts[1].'//'.$hosts[2];
		}

		// maybe host
		return rtrim($url, '/');
	}

	/**
	 * is_same_host
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function is_same_host($base_url, $url)
	{
		$host = static::get_host_from_url($base_url);
		if ($host === false) return false;
		return (substr($url, 0, mb_strlen($host)) === $host);
	}

	/**
	 * is_basic_auth
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function is_basic_auth($url)
	{
		$url = static::avoid_ssl_redirection_loop($url);
		$headers = @get_headers($url, 1);
		if ($headers === false) return false;

		return (strpos($headers[0], 'Authorization Required') !== false);
	}

	/**
	 * basic_auth_prefix
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function basic_auth_prefix($url)
	{
		if (mb_strpos($url, '@') !== false) return $url;

		$setup = Controller_Setup::fetch_setup();
		$basic_user = Arr::get($setup, 'basic_user');
		$basic_pass = Arr::get($setup, 'basic_pass');

		if ( ! static::is_basic_auth($url)) return $url;

		if ($basic_user && $basic_pass)
		{
			return str_replace( '://', '://'.$basic_user.':'.$basic_pass.'@', $url);
		}
		else
		{
			return false;
		}
	}

	/**
	 * remove_basic_auth_prefix
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function remove_basic_auth_prefix($url)
	{
		if (mb_strpos($url, '@') === false) return $url;

		$setup = Controller_Setup::fetch_setup();
		$basic_user = Arr::get($setup, 'basic_user');
		$basic_pass = Arr::get($setup, 'basic_pass');

		return str_replace($basic_user.':'.$basic_pass.'@', '', $url);
	}

	/**
	 * keep_ssl
	 * some redirection redirect to non-ssl url.
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function keep_ssl($url)
	{
		if (empty(static::$target_path)) Util::error('set set_target_path()');

		// do nothing. already ssl
		if (mb_strpos($url, 'https') !== false) return $url;

		// do nothing. originally non ssl
		if (mb_strpos(static::$target_path, 'https') === false) return $url;

		// modify
		if (mb_substr($url, 0, 5) === 'http:')
		{
			return 'https:'.mb_substr($url, 5, mb_strlen($url));
		}
	}

	/**
	 * avoid_ssl_redirection_loop
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function avoid_ssl_redirection_loop($url)
	{
		if (strpos($url, 'https') !== false)
		{
			$url = Util::remove_query_strings($url, array('a11yc'));
			$url = Util::add_query_strings($url, array(array('a11yc', 'ssl')));
		}
		return $url;
	}

	/**
	 * headers
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function headers($url)
	{
		static $headers = array();
		if (isset($headers[$url])) return $headers[$url];

		// basic auth?
		if (static::is_basic_auth($url))
		{
			$url = static::basic_auth_prefix($url);
			$hs = @get_headers($url, 1);

			if ($hs === false)
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_BASIC_AUTH);
				return false;
			}
			elseif (strpos($hs[0], 'Authorization Required') !== false)
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_BASIC_AUTH_WRONG);
				return false;
			}
		}
		// non basic auth
		else
		{
			$hs = @get_headers($url, 1);
		}

		$headers[$url] = $hs;
		return $headers[$url];
	}

	/**
	 * real_url
	 * if this function returns false, real url is not exists.
	 * therefore if this returns sting (url), then target url returned 200.
	 *
	 * @param   string     $url
	 * @return  string|false
	 */
	public static function real_url($url, $depth = 2)
	{
		static $target_url = '';
		static $urls = array();
		static $current_depth = 0;
		if ($current_depth == 0)
		{
			// multibyte url should not decode when recursive.
			$url = Util::urldec($url);
			$target_url = $url;
		}
		if (isset($urls[$target_url])) return $urls[$target_url];

		$tmp = static::avoid_ssl_redirection_loop($url);
		$tmp = static::basic_auth_prefix($tmp);
		$headers = static::headers($tmp);

		// couldn't get headers or max depth
		if (
			$headers === false ||
			$current_depth >= $depth
		)
		{
			$current_depth = 0;
			$urls[$target_url] = false;
			return false;
		}
		// return url
		else if (strpos($headers[0], ' 20') !== false)
		{
			$current_depth = 0;
			$urls[$target_url] = $url;
			return strtolower($url);
		}
		// 30x and it has location
		else if (
			strpos($headers[0], ' 30') !== false &&
			isset($headers['Location'])
		)
		{
			// in case basic auth
			$location = static::basic_auth_prefix($headers['Location']);
			$current_depth++;

			// use first
			if (is_array($location))
			{
				$location = $location[0];
			}

			// recursive
			return static::real_url($location, $depth);
		}
	}

	/**
	 * is html
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function is_html($url)
	{
		return static::fetch_html($url) ? true : false;
	}

	/**
	 * fetch html
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_html($url)
	{
		$target_url = Util::urldec($url);

		static $htmls = array();
		if (isset($htmls[$url])) return $htmls[$url];

		// ssl
		$target_url = static::avoid_ssl_redirection_loop($target_url);

		// check redirect
		$headers = static::headers($target_url);

		if (strpos($headers[0], ' 30') !== false)
		{
			$target_url = static::real_url($target_url);
			if ($target_url === false) return false;
		}
		// 400 or 500
		elseif ( ! strpos($headers[0], ' 20') !== false)
		{
			return false;
		}

		// ssl
		$setup = Controller_Setup::fetch_setup();
		$ignore_cert = array();
		if (strpos($target_url, 'https') !== false)
		{
			$trust_ssl_url = Arr::get($setup, 'trust_ssl_url');
			if ($trust_ssl_url && strpos($target_url, $trust_ssl_url) !== false)
			{
				$ignore_cert = array(
					'verify_peer' => false,
					'verify_peer_name' => false,
				);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_SSL);
				return false;
			}
		}

		// basic_auth?
		$target_url = static::basic_auth_prefix($target_url);

		// is HTML
		$headers = static::headers($target_url);
		$content_types = is_array($headers) ? Arr::get($headers, 'Content-Type') : false;

		 // in case array
		$content_type = is_array($content_types) ? $content_types[0] : $content_types;
		if ( ! $content_types || strpos($content_type, 'text/html') === false)
		{
			return false;
		}

		// try simple file_get_contents()
		$ua = Util::s(Input::user_agent());
		if ($ua)
		{
			$options = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'User-Agent: '.$ua,
				),
				'ssl' => $ignore_cert
			);
			$context = stream_context_create($options);
			$html = @file_get_contents($target_url, false, $context);
		}
		else
		{
			$html = @file_get_contents($target_url);
		}

		if ( ! $html) return false;

		$encodes = array("ASCII", "SJIS-win", "SJIS", "ISO-2022-JP", "EUC-JP");
		$encode = mb_detect_encoding($html, array_merge($encodes, array("UTF-8")));
		if (in_array($encode, $encodes))
		{
			$html = mb_convert_encoding($html, "UTF-8", $encode);
		}
		$htmls[$url] = $html;
		return $html;
	}

	/**
	 * is page exist
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function is_page_exist($url)
	{
		$url = Util::urldec($url);

		// not exists
		$headers = static::headers($url);

		if ($headers === false) return false;

		// exists
		if (strpos($headers[0], ' 20') !== false)
		{
			return true;
		}
		// re-try once
		elseif (strpos($headers[0], ' 30') !== false)
		{
			if (static::real_url($url))
			{
				return true;
			}
		}

		return false;
	}
}
