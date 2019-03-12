<?php
/**
 * A11yc\Model\Version
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Version
{
	protected static $versions = null;
	protected static $current_version = null;
	protected static $targets = array(
		'page',
		'check',
		'setting',
		'result',
		'issue',
		'icl',
		'iclsit',
		'iclchk',
		'html',
	);
	public static $fields = array(
		'version' => 0,
		'name'    => '',
		'trash'   => false,
	);

	/**
	 * get versions
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$versions) && ! $force) return static::$versions;
		$vals = Data::fetchArr('version', 'common', array(), $force, 0);
		static::$versions = Data::deepFilter($vals, static::$fields);
		return static::$versions;
	}

	/**
	 * get version
	 *
	 * @param String $version
	 * @param Bool $force
	 * @return String
	 */
	public static function fetch($version, $force = false)
	{
		return Arr::get(static::fetchAll($force), $version, 0);
	}

	/**
	 * protect
	 *
	 * @return Bool
	 */
	public static function protect()
	{
		$version = date('Ymd');

		// check existence and over-write
		if ( ! empty(static::fetch($version)))
		{
			static::delete($version);
			Session::add('messages', 'errors', A11YC_LANG_RESULT_DELETE_SAMEDATE);
		}

		// insert
		foreach (Data::fetchRaw() as $vals)
		{
			if ( ! in_array($vals['key'], static::$targets)) continue;
			Data::insert($vals['key'], $vals['url'], json_decode($vals['value'], true), $version);
		}

		// update version table
		$vals = static::fetchAll(true);

		$vals[$version] = array(
					'name' => $version,
					'trash' => 0
				);
		self::updateVersions($vals);

		return true;
	}

	/**
	 * delete
	 *
	 * @param INTEGER $version
	 * @return Void
	 */
	public static function delete($version)
	{
		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `version` = ?;';
		Db::execute($sql, array(Data::groupId(true), $version));

		$vals = static::fetchAll(true);
		unset($vals[$version]);
		self::updateVersions($vals);
	}

	/**
	 * update versions
	 *
	 * @param Array $versions
	 * @return Bool
	 */
	public static function updateVersions($versions)
	{
		if (static::fetchAll(true))
		{
			return Data::update('version', 'common', $versions);
		}
		return Data::insert('version', 'common', $versions);
	}

	/**
	 * change version
	 *
	 * @return String|Integer
	 */
	private static function currentVersionRaw()
	{
		return Data::fetchOne('current_version', 'common', false, true, 0);
	}

	/**
	 * current version
	 * depend on QUERY_STRING and Other Setting
	 *
	 * @param Bool $force
	 * @return Integer
	 */
	public static function current($force = false)
	{
		// query string
		$version = Input::get('a11yc_version', 0);
		if (array_key_exists($version, Version::fetchAll()))
		{
			return intval($version);
		}
		if ( ! is_null(static::$current_version) && ! $force) return static::$current_version;
		static::$current_version = self::currentVersionRaw() ?: 0 ;
		return static::$current_version;
	}

	/**
	 * set version
	 *
	 * @param Integer $version
	 * @return Integer
	 */
	public static function setVersion($version)
	{
		// need existence check?
		static::$current_version = $version;
	}

	/**
	 * change version
	 *
	 * @param Integer $version
	 * @return Bool
	 */
	public static function changeVersion($version)
	{
		Session::remove('messages', 'errors');
		if (self::currentVersionRaw())
		{
			return Data::update('current_version', 'common', $version, 0);
		}
		return Data::insert('current_version', 'common', $version, 0);
	}
}
