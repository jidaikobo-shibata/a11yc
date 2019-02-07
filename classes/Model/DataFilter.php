<?php
/**
 * A11yc\Model\DataFilter
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

trait DataFilter
{
	/**
	 * filter
	 *
	 * @param Array $vals
	 * @param Array $fields
	 * @return Array
	 */
	public static function filter($vals, $fields)
	{
		foreach ($fields as $k => $v)
		{
			$vals[$k] = Arr::get($vals, $k, $v);

			// type cast by default value
			if (is_int($v))
			{
				$vals[$k] = intval($vals[$k]);
				continue;
			}

			if (is_bool($v))
			{
				$vals[$k] = (bool) $vals[$k];
				continue;
			}

			if (is_string($v))
			{
				$vals[$k] = trim($vals[$k]);
				continue;
			}

			if (is_array($v) && is_array($vals[$k])) continue;
			if (empty($vals[$k]))
			{
				$vals[$k] = array();
				continue;
			}
			$vals[$k] = array($vals[$k]);
		}

		return $vals;
	}

	/**
	 * deep filter
	 *
	 * @param Array $vals
	 * @param Array $fields
	 * @return Array
	 */
	public static function deepfilter($vals, $fields)
	{
		foreach ($vals as $k => $v)
		{
			$vals[$k] = static::filter($v, $fields);
		}
		return $vals;
	}

	/**
	 * post filter
	 *
	 * @param Array $fields
	 * @return Array
	 */
	public static function postfilter($fields)
	{
		$vals = array();
		foreach ($fields as $k => $v)
		{
			$vals[$k] = is_array($v) ? Input::postArr($k, $v) : Input::post($k, $v);
		}
		return static::filter($vals, $fields);
	}
}
