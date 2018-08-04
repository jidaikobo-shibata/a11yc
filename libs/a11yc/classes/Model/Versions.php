<?php
/**
 * A11yc\Model\Versions
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Versions
{
	protected static $versions = null;
	protected static $tables = array(
		A11YC_TABLE_PAGES,
		A11YC_TABLE_CHECKS,
	);

	/**
	 * get versions
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($force = 0)
	{
		if ( ! is_null(static::$versions) && ! $force) return static::$versions;

		$sql = 'SELECT * FROM '.A11YC_TABLE_VERSIONS.';';
		static::$versions = Db::fetchAll($sql);
		return static::$versions;
	}

	/**
	 * protect
	 *
	 * @return Void
	 */
	public static function protect()
	{
		$status = true;
		$version = date('Ymd');

		// check existence and over-write
		$sql = 'SELECT * FROM '.A11YC_TABLE_VERSIONS.' WHERE `version` = ?;';
		if (Db::fetch($sql, array($version)))
		{
			static::delete($version);
			Session::add('messages', 'messages', A11YC_LANG_RESULTS_DELETE_SAMEDATE);
		}

		// insert
		foreach (static::$tables as $table)
		{
			$records = Db::fetchAll('SELECT * FROM '.$table.' WHERE `version` = 0;');
			// records loop
			foreach ($records as $datas)
			{
				// prepare
				$fields = array();
				$placeholders = array();
				$vals = array();
				foreach ($datas as $field => $data)
				{
					if ($field == 'id') continue;
					$data = $field == 'version' ? $version : $data;
					$fields[] = '`'.$field.'`';
					$placeholders[] = '?';
					$vals[] = $data;
				}

				// sql
				$sql = 'INSERT INTO '.$table.' (';
				$sql.= join(', ', $fields).') VALUES (';
				$sql.= join(', ', $placeholders).');';
				Db::execute($sql, $vals);
			}
		}

		// settings
		$settings = Settings::fetchAll();
		foreach ($settings as $k => $v)
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS.' (`key`, `value`, `version`) ';
			$sql.= ' VALUES (?, ?, ?);';
			$r = Db::execute($sql, array($k, $v, $version));
		}

		if ($status)
		{
			// update version table
			$sql = 'INSERT INTO '.A11YC_TABLE_VERSIONS.' (`version`, `name`) VALUES (?, ?);';
			Db::execute($sql, array($version, $version));
		}

		return $status;
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

		// update
		foreach ($names as $version => $name)
		{
			$sql = 'UPDATE '.A11YC_TABLE_VERSIONS.' SET `name` = ? WHERE `version` = ?;';
			$r = Db::execute($sql, array($name, $version));

			// trash
			$sql = 'UPDATE '.A11YC_TABLE_VERSIONS.' SET `trash` = ? WHERE `version` = ?;';
			$is_trash = isset($is_visible[$version]) ? 0 : 1;
			$r = Db::execute($sql, array($is_trash, $version));
		}

		// delete
		foreach ($deletes as $version)
		{
			static::delete($version);
			Session::add(
				'messages',
				'messages',
				sprintf(A11YC_LANG_PAGES_DELETE_DONE, $names[$version])
			);
		}

		return $r;
	}

	/**
	 * delete
	 *
	 * @param INTEGER $version
	 * @return Void
	 */
	private static function delete($version)
	{
		foreach (static::$tables as $table)
		{
			$sql = 'DELETE FROM '.$table.' WHERE `version` = ?;';
			Db::execute($sql, array($version));
		}

		$sql = 'DELETE FROM '.A11YC_TABLE_VERSIONS.' WHERE `version` = ?;';
		Db::execute($sql, array($version));
	}
}
