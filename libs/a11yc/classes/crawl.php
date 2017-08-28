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
	protected static $target_path = '';

	/**
	 * set_target_path
	 *
	 * @param  String $target_path
	 * @return Void
	 */
	public static function set_target_path($target_path)
	{
		static::$target_path = static::remove_filename($target_path, '/');
	}

	/**
	 * get_target_path
	 *
	 * @return  string
	 */
	public static function get_target_path()
	{
		return static::$target_path;
	}

	/**
	 * is valid scheme
	 *
	 * @param  String $url
	 * @return Bool
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
	 * remove_filename
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function remove_filename($url)
	{
		$url = trim($url);

		// is end with slash?
		$is_not_file = false;
		if (mb_substr($url, -1) == '/')
		{
			$is_not_file = true;
		}

		// is end with file?
		if ( ! $is_not_file)
		{
			$ext = mb_substr($url, strrpos($url, '.') + 1);
			if ($ext && preg_match('/htm|php|pl|cgi/i', $ext))
			{
				$url = dirname($url);
			}
		}

		return Util::urldec($url);
	}

	/**
	 * upper_path
	 *
	 * @param  String $str
	 * @return String
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
	 * @param  String $str ['http://...', '/', '../', '/..']
	 * @return String
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
	 * @param  String $url
	 * @return String|Bool
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
	 * @param  String $url
	 * @return Bool
	 */
	public static function is_same_host($base_url, $url)
	{
		$host = static::get_host_from_url($base_url);
		if ($host === false) return false;
		return (substr($url, 0, mb_strlen($host)) === $host);
	}

	/**
	 * real_url
	 * if this function returns false, real url is not exists.
	 * therefore if this returns sting (url), then target url returned 200.
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function real_url($url, $depth = 2)
	{
		static $urls = array();
		if (isset($urls[$url])) return $urls[$url];

		\A11yc\Guzzle::forge($url);
		$urls[$url] = \A11yc\Guzzle::instance($url)->real_url;

		return $urls[$url];
	}

	/**
	 * is html
	 *
	 * @param  String  $url
	 * @return Bool
	 */
	public static function is_html($url)
	{
		return static::fetch_html($url) ? true : false;
	}

	/**
	 * fetch html
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function fetch_html($url)
	{
		static $htmls = array();
		if (isset($htmls[$url])) return $htmls[$url];

		\A11yc\Guzzle::forge($url);
		\A11yc\Guzzle::instance($url)->set_config('User-Agent', Util::s(Input::user_agent()));

		$htmls[$url] = \A11yc\Guzzle::instance($url)->is_html ?
								 \A11yc\Guzzle::instance($url)->body :
								 false;

		return $htmls[$url];
	}

	/**
	 * is page exist
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function is_page_exist($url)
	{
		\A11yc\Guzzle::forge($url);
		return \A11yc\Guzzle::instance($url)->is_exists;
	}
}
