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
	 * get current uri
	 *
	 * @return String
	 */
	public static function uri()
	{
		$http_host = Input::server("HTTP_HOST");
		$request_uri = Input::server("REQUEST_URI");

		if ($http_host && $request_uri)
		{
			$uri = static::isSsl() ? 'https' : 'http';
			$uri.= '://'.$http_host.rtrim($request_uri, '/');
			return static::s($uri);
		}
		return '';
	}

	/**
	 * add query strings
	 * this medhod doesn't apply sanitizing
	 *
	 * @param String $uri
	 * @param Array $query_strings array(array('key', 'val'),...)
	 * @return String
	 */
	public static function addQueryStrings($uri, $query_strings = array())
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
	 * @param String $uri
	 * @param Array $query_strings array('key',....)
	 * @return String
	 */
	public static function removeQueryStrings($uri, $query_strings = array())
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
	 * @return Bool
	 */
	public static function isSsl()
	{
		return (Input::server("HTTPS") == 'on');
	}

	/**
	 * sanitiz html
	 *
	 * @param String|Array|Bool $str
	 * @return String|Array
	 */
	public static function s($str)
	{
		if (is_bool($str)) return $str;
		if (is_object($str)) return $str;
		if (is_array($str)) return array_map(array('\Kontiki\Util', 's'), $str);
		return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * truncate
	 *
	 * @param String $str
	 * @param Integer $len
	 * @param String $lead
	 * @return String
	 */
	public static function truncate($str, $len, $lead = '...')
	{
		$target_len = mb_strlen($str);
		return $target_len > $len ? mb_substr($str, 0, $len).$lead : $str;
	}

	/**
	 * urlenc
	 *
	 * @param String $url
	 * @return Bool
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
			$url = str_replace('://', '%3A%2F%2F', $url);
		}
		return $url;
	}

	/**
	 * urldec
	 *
	 * @param String $url
	 * @return Bool
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
	 * redirect
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function redirect($url)
	{
		$url = self::urldec($url);
		if (strpos($url, Input::server('HTTP_HOST')) === false) self::error();
		header('location: '.$url);
		exit();
	}

	/**
	 * error
	 *
	 * @param String $message
	 * @return Void
	 */
	public static function error($message = '')
	{
		if ( ! headers_sent())
		{
			header('Content-Type: text/plain; charset=UTF-8', true, 403);
		}
		die(Util::s($message));
	}

	/**
	 * byte2Str
	 *
	 * @param Integer $bytes
	 * @return String
	 * @link http://qiita.com/git6_com/items/ecaafb1afb42fc207814
	 */
	public static function byte2Str($bytes)
	{
		if ( ! is_numeric($bytes)) return $bytes;

		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 1) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 1) . ' KB';
		}
		elseif ($bytes === 0)
		{
			$bytes = '0 bytes';
		}
		else
		{
			$bytes.= $bytes == 1 ? ' byte' : ' bytes';
		}
		return $bytes;
	}

	/**
	 * multisort
	 *
	 * @param Array $array
	 * @param String $by
	 * @param String $order
	 * @return Array
	 */
	public static function multisort($array, $by = 'seq', $order = 'asc')
	{
		$order = strtolower($order) == 'asc' ? SORT_ASC : SORT_DESC ;

		$keys = array();
		foreach($array as $key => $value)
		{
			if ( ! isset($array[$key])) return $array;
			$keys[$key] = Arr::get($value, $by);
		}
		array_multisort($keys, $order, $array);

		return $array;
	}

	/**
	 * keyByColumn
	 * array_column() lower compatible
	 *
	 * @param Array $arr
	 * @param String $column
	 * @return Array
	 */
	public static function keyByColumn($arr, $column = 'id')
	{
		reset($arr);
		if ( ! isset($arr[key($arr)][$column])) return array();
		$vals = array();
		foreach ($arr as $v)
		{
			$id = $v[$column];
			unset($v[$column]);
			$vals[$id] = $v;
		}
		return $vals;
	}
}
