<?php
/**
 * A11yc\Db
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Model;

class Db extends \Kontiki\Db
{
	protected static $version = null;

	/**
	 * init table
	 *
	 * @param String $name
	 * @return Void
	 */
	public static function initTable($name = 'default')
	{
		if (A11YC_DB_TYPE == 'none') return;
		// init default tables
		if (static::isTableExist(A11YC_TABLE_DATA, $name)) return;
		self::initDefault($name);
	}

	/**
	 * init tables
	 *
	 * @param String $name
	 * @return Void
	 */
	private static function initDefault($name = 'default')
	{
		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';
		$auto_increment = A11YC_DB_TYPE == 'mysql' ? 'auto_increment' : '' ;

		// init store table
		$sql = 'CREATE TABLE '.A11YC_TABLE_DATA.' (';
		$sql.= '`id`       INTEGER NOT NULL PRIMARY KEY '.$auto_increment.',';
		$sql.= '`group_id` INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`key`      VARCHAR(256) NOT NULL DEFAULT "",';
		$sql.= '`url`      VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`value`    TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`is_array` BOOL NOT NULL DEFAULT 0,';
		$sql.= '`version`  INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		Db::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_DATA;
			$sql.= ' ADD INDEX a11yc_data_idx(`group_id`, `url`, `key`, `version`)';
			Db::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_data_idx ON '.A11YC_TABLE_DATA;
			$sql.= ' (`group_id`, `url`, `key`, `version`)';
			Db::execute($sql, array(), $name);
		}

		// first create flag
		Model\Data::insert('db_create', 'global', true);
	}

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
	public static function insert($key, $url, $value, $version = 0, $group_id = null)
	{
		if (empty($url) && empty($key)) return false;

		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$group_id = $url == 'global' ? 1 : $group_id;
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
	public static function update($key, $url, $value, $version = 0, $group_id = null)
	{
		// basic value
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$group_id = $url == 'global' ? 1 : $group_id;
		$url = Util::urldec($url);

		// existence criterion value
		$sql = 'SELECT `url`, `key` FROM '.A11YC_TABLE_DATA;
		$sql.= ' WHERE `version` = ? AND `group_id` = ?;';
		$vals = array();
		foreach (Db::fetchAll($sql, array($version, $group_id)) as $v)
		{
			$vals[$v['url']][$v['key']] = true;
		}

		// insert or update
		if( ! isset($vals[$url][$key]) && ! in_array($url, array('common', 'global')))
		{
			$r = static::insert($key, $url, $value, $version, $group_id);
		}
		else
		{
			list($is_array, $value) = self::jsonCheck($value);
			$sql = 'UPDATE '.A11YC_TABLE_DATA.' SET `value` = ?, `is_array` = ?';
			$sql.= ' WHERE `group_id` = ? AND `key` = ? AND `url` = ? AND `version` = ?;';
			$r = Db::execute($sql, array($value, $is_array, $group_id, $key, $url, $version));
		}

		return $r;
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
	public static function updateById($id, $value, $version = 0, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
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
	public static function updateUrl($oldurl, $newurl, $version = 0, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
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
	public static function delete($key, $url, $version = 0, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$url = Util::urldec($url);

		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `key` = ? AND `url` = ? AND `version` = ?;';

		return Db::execute($sql, array($group_id, $key, $url, $version));
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
