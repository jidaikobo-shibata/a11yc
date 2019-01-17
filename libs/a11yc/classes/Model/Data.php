<?php
/**
 * A11yc\Model\Data
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Data
{
	protected static $vals     = null;
	protected static $sites    = null;
	protected static $group_id = null;

	/**
	 * fetch all raw data
	 *
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Array
	 */
	public static function fetchRaw($version = 0, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$sql = 'SELECT * FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `version` = ?;';
		return Db::fetchAll($sql, array($group_id, $version));
	}

	/**
	 * fetch all stored data
	 *
	 * @param Bool $force
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Array
	 */
	public static function fetchAll($force = false, $version = 0, $group_id = null)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$ret = static::fetchRaw($version, $group_id);

		$vals = array();
		foreach ($ret as $v)
		{
			unset($v['group_id']);
			$url   = Arr::get($v, 'url', '_');
			$key   = $v['key'];
			$value = $v['value'];
			$data  = $v['is_array'] ? json_decode($value, true) : $value;

			$is_id = $key == 'issue' || substr($key, 0, 3) == 'icl';
			if (isset($v['id']) && $is_id)
			{
				$data['id'] = intval($v['id']);
			}

			if ($is_id)
			{
				$vals[$url][$key][] = $data;
				continue;
			}
			$vals[$url][$key] = $data;
		}

		ksort($vals);
		static::$vals = $vals;
		return static::$vals;
	}

	/**
	 * fetch stored data
	 *
	 * @param String $key
	 * @param String $url
	 * @param String|Array $default
	 * @param Bool $force
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return String|Array
	 */
	public static function fetch($key, $url = '*', $default = array(), $force = false, $version = 0, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$vals = self::fetchAll($force, $version, $group_id);

		if ($key == 'global')
		{
			return Arr::get($vals, 'global', $default);
		}

		if ($url == '*')
		{
			$retvals = array();
			foreach ($vals as $url => $v)
			{
				if ( ! isset($v[$key])) continue;
				$retvals[$url] = $v[$key];
			}
			return $retvals;
		}

		$vals = Arr::get($vals, $url, array());

		return Arr::get($vals, $key, $default);
	}

	/**
	 * fetch by id
	 *
	 * @param String $id
	 * @return Bool
	 */
	public static function fetchById($id)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_DATA.' WHERE `id` = ?;';
		return Db::fetch($sql, array($id));
	}

	/**
	 * fetch group_id
	 *
	 * @param Bool $force
	 * @return Integer
	 */
	public static function groupId($force = false)
	{
		if ( ! is_null(static::$group_id) && ! $force) return static::$group_id;

		// what a hell is going on! >_<
		// $sql = 'SELECT `value` FROM '.A11YC_TABLE_DATA.' WHERE ';
		// $sql.= '`group_id` = 1 AND `version` = 0 AND ';
		// $sql.= '`url` = "global" AND `key` = "group_id";';
		// $ret = Db::fetch($sql);

		$sql = 'SELECT * FROM '.A11YC_TABLE_DATA.' WHERE `url` = "global";';

		$group_id = 1;
		foreach (Db::fetchAll($sql) as $v)
		{
			if ($v['key'] != 'group_id') continue;
			$group_id = intval($v['value']);
			break;
		}
		static::$group_id = $group_id;

		return static::$group_id;
	}

	/**
	 * fetchSites
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchSites($force = false)
	{
		if ( ! is_null(static::$sites) && ! $force) return static::$sites;
		static::$sites = array();
		$sql = 'SELECT `value` FROM '.A11YC_TABLE_DATA.' WHERE `key` = "sites";';
		$ret = Db::fetch($sql);
		if ($ret)
		{
			static::$sites = json_decode($ret['value'], true);
		}
		return static::$sites;
	}

	/**
	 * fetch current base_url
	 *
	 * @param Bool $force
	 * @return String
	 */
	public static function baseUrl($force = false)
	{
		if (is_null(static::$sites))
		{
			static::fetchSites($force);
		}
		return Arr::get(static::$sites, static::groupId($force), 'https://example.com');
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
	 * @param Array $value
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Integer|Bool
	 */
	public static function insert($key, $url, $value, $version = 0, $group_id = null)
	{
		if (empty($url) && empty($key)) return false;

		$group_id = is_null($group_id) ? self::groupId() : $group_id;
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
		$group_id = is_null($group_id) ? self::groupId() : $group_id;
		$url = Util::urldec($url);

		$vals = self::fetchAll(true);

		if( ! isset($vals[$url][$key]))
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
		$group_id = is_null($group_id) ? self::groupId() : $group_id;
		list($is_array, $value) = self::jsonCheck($value);

		$sql = 'UPDATE '.A11YC_TABLE_DATA.' SET `value` = ?, `is_array` = ?';
		$sql.= ' WHERE `group_id` = ? AND `id` = ? AND `version` = ?;';
		return Db::execute($sql, array($value, $is_array, $group_id, $id, $version));
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
		$group_id = is_null($group_id) ? self::groupId() : $group_id;
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
