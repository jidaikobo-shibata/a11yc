<?php
/**
 * Kontiki\Util
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Util
{
	/**
	 * add autoloader path
	 *
	 * @param   string    $path
	 * @param   string    $namespace
	 * @return  void
	 */
	public static function add_autoloader_path($path, $namespace = '')
	{
		spl_autoload_register(
			function ($class_name) use ($path, $namespace)
			{
				// check namespace
				$class = strtolower($class_name);
				$strlen = strlen($namespace);

				if (substr($class, 0, $strlen) !== $namespace) return;
				$class = substr($class, $strlen + 1);

				// underscores are directories
				$path.= str_replace('_', '/', $class);

				// require
				$file_path = $path.'.php';
				if (file_exists($file_path))
				{
					require $path.'.php';
				}
				else
				{
					return false;
				}

				// init
				if (method_exists($class_name, '_init') and is_callable($class_name.'::_init'))
				{
					call_user_func($class_name.'::_init');
				}
			}
		);
	}

	/**
	 * get current uri
	 *
	 * @return  string
	 */
	public static function uri()
	{
		$uri = static::is_ssl() ? 'https' : 'http';
		$uri.= '://'.$_SERVER["HTTP_HOST"].rtrim($_SERVER["REQUEST_URI"], '/');
		return static::s($uri);
	}

	/**
	 * add query strings
	 * this medhod doesn't apply sanitizing
	 *
	 * @param   string    $uri
	 * @param   array     $query_strings array(array('key', 'val'),...)
	 * @return  string
	 */
	public static function add_query_strings($uri, $query_strings = array())
	{
		$delimiter = strpos($uri, '?') !== false ? '&amp;' : '?';
		$qs = array();
		foreach ($query_strings as $v)
		{
			// if (is_array($v))
			$qs[] = $v[0].'='.$v[1];
		}
		return $uri.$delimiter.join('&amp;', $qs);
	}

	/**
	 * remove query strings
	 *
	 * @param   string    $uri
	 * @param   array     $query_strings array('key',....)
	 * @return  string
	 */
	public static function remove_query_strings($uri, $query_strings = array())
	{
		if (strpos($uri, '?') !== false)
		{
			// all query strings
			$query_strings = $query_strings ?: array_keys($_GET);

			// replace
			$uri = str_replace('&amp;', '&', $uri);
			$pos = strpos($uri, '?');
			$base_url = substr($uri, 0, $pos);
			$qs = explode('&', substr($uri, $pos + 1));
			foreach ($qs as $k => $v)
			{
				foreach ($query_strings as $vv)
				{
					if (substr($v, 0, strpos($v, '=')) == $vv)
					{
						unset($qs[$k]);
					}
				}
			}
			$uri = $qs ? $base_url.'?'.join('&amp;', $qs) : $base_url;
		}
		return $uri;
	}

	/**
	 * is ssl
	 *
	 * @return  bool
	 */
	public static function is_ssl()
	{
		return isset($_SERVER['HTTP_X_SAKURA_FORWARDED_FOR']) ||
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']);
	}

	/**
	 * sanitiz html
	 *
	 * @return  string | array
	 */
	public static function s($str)
	{
		if (is_array($str)) return array_map(array('\Kontiki\Util', 's'), $str);
		return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * urlenc
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function urlenc($url)
	{
		$url = str_replace(array("\n", "\r"), '', $url);
		$url = static::s($url); // & to &amp;
		$url = str_replace(' ', '%20', $url);
		if (strpos($url, '%') === false)
		{
			$url = urlencode($url);
		}
		else
		{
			$url = str_replace(
				'://',
				'%3A%2F%2F',
				$url);
		}
		return $url;
	}

	/**
	 * urldec
	 *
	 * @param   string     $url
	 * @return  bool
	 */
	public static function urldec($url)
	{
		$url = str_replace(array("\n", "\r"), '', $url);
		$url = trim($url);
		$url = rtrim($url, '/');
		$url = static::urlenc($url);
		$url = urldecode($url);
		$url = str_replace('&amp;', '&', $url);
		return $url;
	}

	/**
	 * error
	 *
	 * @return  void
	 */
	public static function error($message = '')
	{
		if ( ! headers_sent())
		{
			header('Content-Type: text/plain; charset=UTF-8', true, 403);
		}
		die(Util::s($message));
	}
}
