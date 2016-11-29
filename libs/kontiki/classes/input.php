<?php
/**
 * Kontiki\Input
 *
 * @package    part of Kontiki
 * @forked     FuelPHP core/classes/input.php
 * @version    1.0
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
	public static function get($index = null, $default = null)
	{
		if (isset($_GET[$index]))
		{
			return $_GET[$index];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		return $_GET;
	}

	/**
	 * Fetch an item from the POST array
	 *
	 * @param   string  The index key
	 * @param   mixed   The default value
	 * @return  string|array
	 */
	public static function post($index = null, $default = null)
	{
		if (isset($_POST[$index]))
		{
			return $_POST[$index];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		return $_POST;
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
		if (isset($_FILES[$index]))
		{
			return $_FILES[$index];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		return $_FILES;
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
		if (isset($_COOKIE[$index]))
		{
			return $_COOKIE[$index];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		return $_COOKIE;
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
		if (isset($_SERVER[$index]))
		{
			return $_SERVER[$index];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		return $_SERVER;
	}
}
