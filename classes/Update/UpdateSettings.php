<?php
/**
 * A11yc\Update\UpdateSettings
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class UpdateSettings
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS;
		$data = Db::fetchAll($sql);

		self::dropSettings();
		self::createSettings('default');

		foreach ($data as $each)
		{
			$version = intval($each['version']);
			foreach ($each as $k => $v)
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS;
				$sql.= ' (`key`, `value`, `version`) VALUES (?, ?, ?);';
				Db::execute($sql, array($k, $v, $version));
			}
		}
	}

	/**
	 * createSettings
	 *
	 * @return Void
	 */
	protected static function createSettings($name)
	{
		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';
		// init setups
		$sql = 'CREATE TABLE '.A11YC_TABLE_SETTINGS.' (';
		$sql.= '`key`     TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`value`   TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`version` INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		Db::execute($sql, array(), $name);
	}

	/**
	 * dropSettings
	 *
	 * @return Void
	 */
	protected static function dropSettings()
	{
		$sql = 'DROP TABLE '.A11YC_TABLE_SETTINGS;
		Db::execute($sql);
	}
}
