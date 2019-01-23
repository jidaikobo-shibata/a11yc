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
	protected static $keys = array(
		'page',
		'check',
		'setting',
		'result',
		'issue',
		'icl',
		'iclsit',
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
		static::$versions = Data::fetch('version', 'common', array(), $force, 0);
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
			self::delete($version);
			Session::add('messages', 'messages', A11YC_LANG_RESULT_DELETE_SAMEDATE);
		}

		// insert
		foreach (Data::fetchRaw() as $vals)
		{
			if ( ! in_array($vals['key'], static::$keys)) continue;
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
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		$names   = Input::postArr('name');
		$trashes = Input::postArr('trash');
		$deletes = Input::postArr('delete');

		// update
		$vals = array();
		foreach ($names as $version => $name)
		{
			$vals[$version] = array(
				'name'    => $name,
				'trash'   => isset($trashes[$version]) ? 0 : 1,
			);
		}
		$r = Data::update('version', 'common', $vals);

		// delete
		foreach ($deletes as $version)
		{
			self::delete($version);
			Session::add(
				'messages',
				'messages',
				sprintf(A11YC_LANG_CTRL_DELETE_DONE, $names[$version])
			);
		}

		return $r;
	}

	/**
	 * update versions
	 *
	 * @param Array $versions
	 * @return Void
	 */
	private static function updateVersions($versions)
	{
		return Data::update('version', 'common', $versions);
	}

	/**
	 * delete
	 *
	 * @param INTEGER $version
	 * @return Void
	 */
	private static function delete($version)
	{
		$sql = 'DELETE FROM '.A11YC_TABLE_DATA.' WHERE ';
		$sql.= '`group_id` = ? AND `version` = ?;';
		Db::execute($sql, array(Data::groupId(true), $version));

		$vals = static::fetchAll(true);
		unset($vals[$version]);
		self::updateVersions($vals);
	}
}
