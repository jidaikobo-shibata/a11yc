<?php
/**
 * A11yc\Model\Iclchk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Iclchk
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
		$vals = Data::fetchArr('iclchk', $url, array(), $force);
		static::$checks[$url] = array_map('intval', Arr::get($vals, 0, array()));
		unset(static::$checks[$url]['id']);
		return static::$checks[$url];
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
		$vals = array_map('intval', $vals);
		self::delete($url);
		return Data::insert('iclchk', $url, $vals);
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
		Data::delete('iclchk', $url);
	}
}
