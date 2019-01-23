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
		if (isset(static::$checks[$url]) && ! $force) return static::$checks[$url];
		$vals = Data::fetch('check', $url, array(), $force);
		$vals = is_array($vals) ? $vals : array();

		static::$checks[$url] = array();
		foreach ($vals as $criterion => $val)
		{
			foreach ($val as $code => $v)
			{
				static::$checks[$url][$criterion][$code] = $v;
			}
		}

		return static::$checks[$url];
	}

	/**
	 * fetch failures
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function fetchFailures($url = '')
	{
		$vals = Data::fetch('check');
		$vals = is_array($vals) ? $vals : array();

		foreach ($vals as $each_url => $val)
		{
			foreach ($val as $criterion => $v)
			{
				foreach ($v as $kk => $vv)
				{
					if (substr($vv, 0, 1) != 'F') unset($vals[$each_url][$criterion][$kk]);
				}
				if (empty($vals[$each_url][$criterion])) unset($vals[$each_url][$criterion]);
			}
			if (empty($vals[$each_url])) unset($vals[$each_url]);
		}

		if (empty($url))
		{
			return $vals;
		}

		return Arr::get($vals, $url, array());
	}

	/**
	 * insert results
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insert($url, $vals)
	{
		return Data::insert('check', $url, $vals);
	}

	/**
	 * update
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Void
	 */
	public static function update($url, $vals)
	{
		self::delete($url);
		static::insert($url, $vals);
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
	}
}
