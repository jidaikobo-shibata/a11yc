<?php
/**
 * Kontiki\Input
 *
 * @package    part of Kontiki
 * @forked     FuelPHP core/classes/input.php
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Input
{
	/**
	 * Return's the referrer
	 *
	 * @return  string
	 */
	public static function referrer($default = '')
	{
		return static::server('HTTP_REFERER', $default);
	}

	/**
	 * Return's the user agent
	 *
	 * @return  string
	 */
	public static function user_agent($default = '')
	{
		return static::server('HTTP_USER_AGENT', $default);
	}

	/**
	 * Gets the specified GET variable.
	 *
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @return  string|array
	 */
	public static function get($index = null, $default = null, $sanitize = TRUE)
	{
		$get = $_GET;

		if ($sanitize)
		{
//			$get = array_map(function($str) {return str_replace("\0", '', $str);}, $get);
		}
		if (func_num_args() === 0)
		{
			return $get;
		}

		if ($index && isset($get[$index]))
		{
			return $get[$index];
		}

		return $default;
	}

	/**
	 * Fetch an item from the POST array
	 *
	 * @param   string  The index key
	 * @param   mixed   The default value
	 * @return  string|array
	 */
	public static function post($index = null, $default = null, $sanitize = TRUE)
	{
		$post = $_POST;

		if ($sanitize)
		{
//			$post = array_map(function($str) {return str_replace("\0", '', $str);}, $post);
		}

		if (func_num_args() === 0)
		{
			return $post;
		}

		if ($index && isset($post[$index]))
		{
			return $post[$index];
		}

		return $default;
	}

	/**
	 * Fetch an item from the FILE array
	 *
	 * @param   string  The index key
	 * @param   mixed   The default value
	 * @return  string|array
	 */
	public static function file($index = null, $default = null)
	{
		if (func_num_args() === 0)
		{
			return $_FILES;
		}

		if ($index && isset($_FILES[$index]))
		{
			return $_FILES[$index];
		}

		return $default;
	}

	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @param    string  The index key
	 * @param    mixed   The default value
	 * @return   string|array
	 */
	public static function cookie($index = null, $default = null)
	{
		if (func_num_args() === 0)
		{
			return $_COOKIES;
		}

		if ($index && isset($_COOKIES[$index]))
		{
			return $_COOKIES[$index];
		}

		return $default;
	}

	/**
	 * Fetch an item from the SERVER array
	 *
	 * @param   string  The index key
	 * @param   mixed   The default value
	 * @return  string|array
	 */
	public static function server($index = null, $default = null)
	{
		if (func_num_args() === 0)
		{
			return $_SERVER;
		}

		if ($index && isset($_SERVER[$index]))
		{
			return $_SERVER[$index];
		}

		return $default;
	}
}
