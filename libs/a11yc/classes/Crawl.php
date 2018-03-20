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

	/**
	 * is target webpage
	 *
	 * @param  String  $url
	 * @return Bool
	 */
	public static function isTargetMime($url)
	{
		Guzzle::forge($url);

		if (isset(Guzzle::instance($url)->headers['Content-Type'][0]))
		{
			$mime = Guzzle::instance($url)->headers['Content-Type'][0];
			if (strpos($mime, ';') !== false)
			{
				$mime = substr($mime, 0 ,strrpos($mime, ';'));
			}

			return in_array($mime, Values::targetMimes());
		}
		return false;
	}

	/**
	 * is page exist
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function isPageExist($url)
	{
		Guzzle::forge($url);
		return Guzzle::instance($url)->is_exists;
	}




	protected static $target_path = '';
	protected static $target_path_raw = '';
	protected static $real_urls = array();

	/**
	 * set_target_path
	 *
	 * @param  String $target_path
	 * @return Void
	 */
	public static function setTargetPath($target_path)
	{
		static::$target_path = static::remove_filename($target_path, '/');
		static::$target_path_raw = $target_path;
	}

	/**
	 * get target path
	 *
	 * @param  Bool $is_raw
	 * @return  string
	 */
	public static function getTargetPath($is_raw = false)
	{
		if ($is_raw) return static::$target_path_raw;
		return static::$target_path;
	}

	/**
	 * is valid scheme
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function isValidScheme($url)
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
	public static function removeFilename($url)
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
	 * real_url
	 * if this function returns false, real url is not exists.
	 * therefore if this returns sting (url), then target url returned 200.
	 *
	 * @param  String $url
	 * @return String
	 */
	public static function real_url($url, $depth = 2)
	{
		if (isset(static::$real_urls[$url])) return static::$real_urls[$url];

		Guzzle::forge($url);
		$real_url = Guzzle::instance($url)->real_url;

		if ($real_url)
		{
			static::$real_urls[$url] = $real_url;
		}
		// it maybe guzzle's trouble
		elseif ( ! empty($url))
		{
			static::$real_urls[$url] = $url;
		}

		return isset(static::$real_urls[$url]) ? static::$real_urls[$url] : $url;
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
}
