<?php
/**
 * A11yc\Model\DataUpdate
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

trait DataUpdate
{
	/**
	 * jsonCheck
	 *
	 * @param Srting|Array $vals
	 * @return Array
	 */
	private static function jsonCheck($vals)
	{
		$is_array = false;
		if (is_array($vals))
		{
			$vals = json_encode($vals);
			$is_array = true;
		}
		return array($is_array, $vals);
	}

	/**
	 * insert
	 *
	 * @param String $key
	 * @param String $url
	 * @param Mixed $value
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Integer|Bool
	 */
	public static function insert($key, $url, $value, $version = null, $group_id = null)
	{
		if (empty($url) && empty($key)) return false;

		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$group_id = $url == 'global' ? 1 : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		$url = Util::urldec($url);
		list($is_array, $value) = self::jsonCheck($value);

		$sql = 'INSERT INTO '.A11YC_TABLE_DATA.' (';
		$sql.= '`group_id`,';
		$sql.= '`key`,';
		$sql.= '`url`,';
		$sql.= '`value`,';
		$sql.= '`is_array`,';
		$sql.= '`version`';
		$sql.= ')';
		$sql.= ' VALUES (?, ?, ?, ?, ?, ?);';

		$r = Db::execute($sql, array($group_id, $key, $url, $value, $is_array, $version));

		if ( ! $r) return false;

		$sql = 'SELECT `id` FROM '.A11YC_TABLE_DATA;
		$sql.= ' ORDER BY `id` desc LIMIT 1;';
		$data = Db::fetch($sql);

		return isset($data['id']) ? intval($data['id']) : false;
	}

	/**
	 * update
	 *
	 * @param String $key
	 * @param String $url
	 * @param Mixed $value
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Bool
	 */
	public static function update($key, $url, $value, $version = null, $group_id = null)
	{
		// basic value
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$group_id = $url == 'global' ? 1 : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		$url = Util::urldec($url);
		list($is_array, $value) = self::jsonCheck($value);

		$sql = 'UPDATE '.A11YC_TABLE_DATA.' SET `value` = ?, `is_array` = ?';
		$sql.= ' WHERE `group_id` = ? AND `key` = ? AND `url` = ? AND `version` = ?;';

		return Db::execute($sql, array($value, $is_array, $group_id, $key, $url, $version));
	}

	/**
	 * update by id
	 *
	 * @param Integer $id
	 * @param Mixed $value
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Bool
	 */
	public static function updateById($id, $value, $version = null, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		list($is_array, $value) = self::jsonCheck($value);

		$sql = 'UPDATE '.A11YC_TABLE_DATA.' SET `value` = ?, `is_array` = ?';
		$sql.= ' WHERE `group_id` = ? AND `id` = ? AND `version` = ?;';
		return Db::execute($sql, array($value, $is_array, $group_id, $id, $version));
	}

	/**
	 * update url
	 *
	 * @param String $oldurl
	 * @param String $newurl
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Bool
	 */
	public static function updateUrl($oldurl, $newurl, $version = null, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		$oldurl = trim($oldurl);
		$newurl = trim($newurl);

		$sql = 'UPDATE '.A11YC_TABLE_DATA.' SET `url` =';
		$sql.= ' REPLACE (`url`, ?, ?)';
		$sql.= ' WHERE `group_id` = ? AND `url` LIKE ? AND `version` = ?;';
		return Db::execute($sql, array($oldurl, $newurl, $group_id, $oldurl.'%', $version));
	}

	/**
	 * delete
	 *
	 * @param String $key
	 * @param String $url
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Bool
	 */
	public static function delete($key, $url, $version = null, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		$url = Util::urldec($url);

		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `key` = ? AND `url` = ? AND `version` = ?;';

		return Db::execute($sql, array($group_id, $key, $url, $version));
	}

	/**
	 * delete by key
	 *
	 * @param String $key
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Bool
	 */
	public static function deleteByKey($key, $version = null, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$version = is_null($version) ? Version::current() : $version;
		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `key` = ? AND `version` = ?;';
		return Db::execute($sql, array($group_id, $key, $version));
	}

	/**
	 * delete by id
	 *
	 * @param Integer $id
	 * @return Bool
	 */
	public static function deleteById($id)
	{
		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE `id` = ?;';
		return Db::execute($sql, array($id));
	}
}
