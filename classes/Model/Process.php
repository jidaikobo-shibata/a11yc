<?php
/**
 * A11yc\Model\Process
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Process
{
	protected static $vals = null;
	public static $fields = array(
		'status'  => 'yet',
		'vals' => array(),
	);
	public static $each_fields = array(
		'result' => '',
		'techs'   => array(),
		'memo'    => '',
	);

	/**
	 * fetch raw
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchRaw($url, $force = false)
	{
//		Data::deleteByKey('process');
		return Data::fetchArr('process', Util::urldec($url), array(), $force);
	}

	/**
	 * fetch all
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		static::$vals = array();
		foreach (self::fetchRaw('*', $force) as $url => $v)
		{
			static::$vals[$url] = self::filter($v);
		}
		return static::$vals;
	}

	/**
	 * fetch one
	 *
	 * @param String $url
	 * @param Array $default
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($url, $default = array(), $force = false)
	{
		if (empty($url)) Util::error();
		$vals = self::fetchAll($force);
		return Arr::get($vals, Util::urldec($url), $default);
	}

	/**
	 * filter
	 *
	 * @param Array $vals
	 * @return Array
	 */
	private static function filter($vals)
	{
		foreach ($vals as $k => $v)
		{
			$vals[$k] = Data::filter($v, static::$fields);
			foreach ($vals as $kk => $vv)
			{
				$vals[$k][$kk]['vals'] = Data::filter($vv, static::$each_fields);
			}
		}
		return $vals;
	}

	/**
	 * insert
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insert($url, $vals)
	{
		return Data::insert('process', $url, self::filter($vals));
	}

	/**
	 * update
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Bool
	 */
	public static function update($url, $vals)
	{
//		Data::deleteByKey('process');

		$current = static::fetch($url, array(), true);

		foreach ($vals as $k => $v)
		{
			$current[$k] = $vals[$k];
		}
		return Data::update('process', $url, self::filter($current));
	}
}
