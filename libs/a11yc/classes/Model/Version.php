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
		static::$versions = Data::fetch('versions', 'global', 0, $force);
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
		return Arr::get(static::fetchAll($force), $version, '');
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
		foreach (static::$keys as $key)
		{
			foreach (Data::fetch($key) as $vals)
			{
				Data::insert($key, '', $vals,$version);
			}
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
		$names      = Input::postArr('name');
		$is_visible = Input::postArr('trash');
		$deletes    = Input::postArr('delete');
		$r          = false;

		// update
		foreach ($names as $version => $name)
		{
			$key = self::keyname($version);
			$is_trash = isset($is_visible[$version]) ? 0 : 1;
			$vals = array(
				'version' => $version,
				'name'    => $name,
				'trash'   => $is_trash,
			);
			$r = Setting::update($key, $vals);
		}

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
		return Data::update('versions', 'global',$versions);
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
