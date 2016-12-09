<?php
/**
 * Kontiki\Arr
 *
 * @package    part of Kontiki
 * @forked     FuelPHP core/classes/arr.php
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Arr
{
	/**
	 * Gets a key from an array, with a default value if it does
	 * not exist.
	 *
	 * @param   array   $array    The search array
	 * @param   mixed   $key      The dot-notated key or array of keys
	 * @param   string  $default  The default value
	 * @return  mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
		{
			throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
		}

		if (is_null($key))
		{
			return $array;
		}

		if (is_array($key))
		{
			$return = array();
			foreach ($key as $k)
			{
				$return[$k] = static::get($array, $k, $default);
			}
			return $return;
		}

		is_object($key) and $key = (string) $key;

		if (array_key_exists($key, $array))
		{
			return $array[$key];
		}

		return $array;
	}

	/**
	 * Set an array item to the value.
	 *
	 * @param   array   $array  The array to insert it into
	 * @param   mixed   $key    The dot-notated key to set or array of keys
	 * @param   mixed   $value  The value
	 * @return  void
	 */
	public static function set(&$array, $key, $value = null)
	{
		if (is_null($key))
		{
			$array = $value;
			return;
		}

		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				static::set($array, $k, $v);
			}
		}
	}
}