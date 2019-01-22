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
	 * @param String  $url
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
	 * @param String $url
	 * @return Bool
	 */
	public static function isPageExist($url)
	{
		if (Guzzle::envCheck() === false) return false;
		Guzzle::forge($url);
		return Guzzle::instance($url)->is_exists;
	}

	protected static $target_path = '';
	protected static $target_path_raw = '';
	protected static $real_urls = array();

	/**
	 * set target path
	 *
	 * @param String $target_path
	 * @return Void
	 */
	public static function setTargetPath($target_path)
	{
		static::$target_path = static::removeFilename($target_path);
		static::$target_path_raw = $target_path;
	}

	/**
	 * get target path
	 *
	 * @param Bool $is_raw
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
	 * @param String $url
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
	 * @param String $url
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
	 * upperPath
	 *
	 * @param String $str
	 * @return String
	 */
	public static function upperPath($str)
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
	 * get host from url
	 *
	 * @param String $url
	 * @return String|Bool
	 */
	public static function getHostFromUrl($url)
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
	 * @param String $url
	 * @return String
	 */
	public static function real_url($url)
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
}
