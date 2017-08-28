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
	 * @param  String $default
	 * @return String
	 */
	public static function referrer($default = '')
	{
		return static::server('HTTP_REFERER', $default);
	}

	/**
	 * Return's the user agent
	 *
	 * @param  String $default
	 * @return String
	 */
	public static function user_agent($default = '')
	{
		return static::server('HTTP_USER_AGENT', $default);
	}

	/**
	 * Check Post data existence
	 *
	 * @return Bool
	 */
	public static function is_post_exists()
	{
		return (static::server('REQUEST_METHOD') == 'POST');
	}

	/**
	 * Gets the specified GET variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @param  String $options for filter_input()
	 * @return String|Array
	 */
	public static function get(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT,
		$options = array()
	)
	{
		$val = filter_input(INPUT_GET, $index, $filter, $options);
		return $val ? $val : $default;
	}

	/**
	 * Gets the specified Array GET variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @return String|Array
	 */
	public static function get_arr(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT
	)
	{
		return static::get($index, array(), $filter, FILTER_REQUIRE_ARRAY);
	}

	/**
	 * Gets the specified POST variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @param  String  $options  for filter_input()
	 * @return String|Array
	 */
	public static function post(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT,
		$options = array()
	)
	{
		$val = filter_input(INPUT_POST, $index, $filter, $options);
		return $val ? $val : $default;
	}

	/**
	 * Gets the specified Array POST variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @return String|Array
	 */
	public static function post_arr(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT
	)
	{
		return static::post($index, array(), $filter, FILTER_REQUIRE_ARRAY);
	}

	/**
	 * Gets the specified COOKIE variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @param  String $options for filter_input()
	 * @return String|Array
	 */
	public static function cookie(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT,
		$options = array()
	)
	{
		$val = filter_input(INPUT_COOKIE, $index, $filter, $options);
		return $val ? $val : $default;
	}

	/**
	 * Gets the specified SERVER variable.
	 *
	 * @param  String $index The index to get
	 * @param  String $default The default value
	 * @param  String $filter default: FILTER_DEFAULT
	 * @param  String $options for filter_input()
	 * @return String|Array
	 */
	public static function server(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT,
		$options = array()
	)
	{
		$val = filter_input(INPUT_SERVER, $index, $filter, $options);
		if ( ! $val)
		{
			$val = filter_input(INPUT_ENV, $index, $filter, $options);
		}
		return $val ? $val : $default;
	}

	/**
	 * Fetch an item from the FILE array
	 *
	 * @param  String $index The index to get
	 * @param  Mixed $default The default value
	 * @return String|Array
	 */
	public static function file($index, $default = null)
	{
		$files = $_FILES;

		if (func_num_args() === 0)
		{
			return $files;
		}

		if ($index && isset($files[$index]))
		{
			return $files[$index];
		}

		return $default;
	}
}
