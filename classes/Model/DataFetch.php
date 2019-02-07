<?php
/**
 * A11yc\Model\DataFetch
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

trait DataFetch
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
	public static function fetchRaw($version = null, $group_id = null)
	{
		$group_id = is_null($group_id) ? static::groupId() : $group_id;
		$version = is_null($version) ? self::versionByQuerystring() : $version;
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
	public static function fetchAll($force = false, $version = null, $group_id = null)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		$ret = static::fetchRaw($version, $group_id);

		$vals = array();
		foreach ($ret as $v)
		{
			unset($v['group_id']);
			$url   = Arr::get($v, 'url', '_');
			$key   = $v['key'];
			$value = $v['value'];
			$data  = $v['is_array'] ? json_decode($value, true) : $value;
			//if ($key == 'html') continue;

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
	 * @param String $url `url`|common|global|*
	 * @param Mixed $default
	 * @param Bool $force
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return String|Array
	 */
	public static function fetch($key, $url = '*', $default = array(), $force = false, $version = null, $group_id = null)
	{
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
	 * fetch stored data by array
	 *
	 * @param String $key
	 * @param String $url `url`|common|global|*
	 * @param Mixed $default
	 * @param Bool $force
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Array
	 */
	public static function fetchArr($key, $url = '*', $default = array(), $force = false, $version = null, $group_id = null)
	{
		$vals = static::fetch($key, $url, $default, $force, $version, $group_id);
		return is_array($vals) ? $vals : array();
	}

	/**
	 * fetch stored data by one
	 *
	 * @param String $key
	 * @param String $url `url`|common|global|*
	 * @param Mixed $default
	 * @param Bool $force
	 * @param Integer $version
	 * @param Integer $group_id
	 * @return Array
	 */
	public static function fetchOne($key, $url = '*', $default = '', $force = false, $version = null, $group_id = null)
	{
		$vals = static::fetch($key, $url, $default, $force, $version, $group_id);
		return ! is_array($vals) ? $vals : $default;
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
	 * versionByQuerystring
	 * depend on QUERY_STRING
	 *
	 * @return Integer
	 */
	private static function versionByQuerystring()
	{
		$version = Input::get('a11yc_version', 0);
		if (array_key_exists($version, Version::fetchAll()))
		{
			return intval($version);
		}
		return 0;
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
		static::$group_id = static::fetchGroupId();
		return static::$group_id;
	}

	/**
	 * fetch group_id
	 *
	 * @return Integer|Bool
	 */
	public static function fetchGroupId()
	{
		$sql = 'SELECT `value` FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = 1 AND `version` = 0 AND ';
		$sql.= '`url` = "global" AND `key` LIKE "group_id%";';
		// what a hell is going on! "equal" wouldn't work >_<
		// $sql.= '`url` = "global" AND `key` = "group_id";';
		$ret = Db::fetch($sql);

		if ($ret === false) static::insert('group_id', 'global', 1, 0, 1);
		return isset($ret['value']) ? intval($ret['value']) : 1;
	}

	/**
	 * set group_id
	 *
	 * @param Integer $group_id
	 * @return Void
	 */
	public static function setGroupId($group_id)
	{
		// need existence check?
		static::$group_id = $group_id;
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
