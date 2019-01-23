<?php
/**
 * Kontiki\Arr
 *
 * @package    part of Kontiki
 * @forked     FuelPHP core/classes/arr.php
 * @forked     FuelPHP core/classes/fuel.php
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Arr
{
	/**
	 * Takes a value and checks if it is a Closure or not, if it is it
	 * will return the result of the closure, if not, it will simply return the
	 * value.
	 *
	 * @param  mixed  $var  The value to get
	 * @return  mixed
	 */
	public static function value($var)
	{
		return ($var instanceof \Closure) ? $var() : $var;
	}

	/**
	 * Gets a dot-notated key from an array, with a default value if it does
	 * not exist.
	 *
	 * @param  array   $array    The search array
	 * @param  mixed   $key      The dot-notated key or array of keys
	 * @param  string  $default  The default value
	 * @return  mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
		{
//			throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
			return false;
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
		is_bool($key) and $key = '';

		if (array_key_exists($key, $array))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $key_part)
		{
			if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false)
			{
				if ( ! is_array($array) or ! array_key_exists($key_part, $array))
				{
					return static::value($default);
				}
			}

			$array = $array[$key_part];
		}

		return $array;
	}

	/**
	 * Set an array item (dot-notated) to the value.
	 *
	 * @param  array   $array  The array to insert it into
	 * @param  mixed   $key    The dot-notated key to set or array of keys
	 * @param  mixed   $value  The value
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
		else
		{
			$keys = explode('.', $key);

			while (count($keys) > 1)
			{
				$key = array_shift($keys);

				if ( ! isset($array[$key]) or ! is_array($array[$key]))
				{
					$array[$key] = array();
				}

				$array =& $array[$key];
			}

			$array[array_shift($keys)] = $value;
		}
	}
}
