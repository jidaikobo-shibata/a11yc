<?php
/**
 * A11yc\Model\Checklist
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Checklist
{
	protected static $checks = null;

	/**
	 * fetch checks from db
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($url, $force = false)
	{
		if (empty($url)) return array();
		$url = Util::urldec($url);
		if (isset(static::$checks[$url]) && ! $force) return static::$checks[$url];
		static::$checks[$url] = Data::fetchArr('check', $url, array(), $force);
		return static::$checks[$url];
	}

	/**
	 * fetch failures
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function fetchFailures($url = '*')
	{
		return Data::fetchArr('check_failure', $url, array());
	}

	/**
	 * filter failure
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	public static function filterFailure($vals)
	{
		$failures = array();
		foreach ($vals as $criterion => $val)
		{
			if ( ! is_array($val)) continue;
			foreach ($val as $v)
			{
				if (substr($v, 0, 1) != 'F') continue;
				$failures[$criterion][] = $v;
			}
		}
		return $failures;
	}

	/**
	 * insert
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Integer|Bool
	 */
	public static function insert($url, $vals)
	{
		Data::insert('check_failure', $url, static::filterFailure($vals));
		return Data::insert('check', $url, $vals);
	}

	/**
	 * update
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Integer|Bool
	 */
	public static function update($url, $vals)
	{
		self::delete($url);
		return static::insert($url, $vals);
	}

	/**
	 * delete
	 *
	 * @param String $url
	 * @return Bool
	 */
	public static function delete($url)
	{
		Data::delete('check', $url);
		Data::delete('check_failure', $url);
	}
}
