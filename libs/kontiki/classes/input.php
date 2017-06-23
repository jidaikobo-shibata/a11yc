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
	 * Check Post data existence
	 *
	 * @return  bool
	 */
	public static function is_post_exists()
	{
		return (static::server('REQUEST_METHOD') == 'POST');
	}

	/**
	 * Gets the specified GET variable.
	 *
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @param   string  $options  for filter_input()
	 * @return  string|array
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
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @return  string|array
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
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @param   string  $options  for filter_input()
	 * @return  string|array
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
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @return  string|array
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
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @param   string  $options  for filter_input()
	 * @return  string|array
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
	 * @param   string  $index    The index to get
	 * @param   string  $default  The default value
	 * @param   string  $filter   default: FILTER_DEFAULT
	 * @param   string  $options  for filter_input()
	 * @return  string|array
	 */
	public static function server(
		$index,
		$default = null,
		$filter = FILTER_DEFAULT,
		$options = array()
	)
	{
		$val = filter_input(INPUT_SERVER, $index, $filter, $options);
		return $val ? $val : $default;
	}

	/**
	 * Fetch an item from the FILE array
	 *
	 * @param   string  The index key
	 * @param   mixed   The default value
	 * @return  string|array
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
