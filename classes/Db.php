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
	 * build version sql
	 *
	 * @param bool $is_and
	 * @return string
	 */
	public static function versionSql($is_and = true)
	{
		$sql = ' `version` = '.self::getVersion();
		return $is_and ? ' AND'.$sql : ' WHERE'.$sql ;
	}

	/**
	 * build version sql
	 *
	 * @param bool $is_and
	 * @return string
	 */
	public static function currentVersionSql($is_and = true)
	{
		$sql = ' `version` = 0';
		return $is_and ? ' AND'.$sql : ' WHERE'.$sql ;
	}

	/**
	 * get version
	 * depend on user request value
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		if ( ! is_null(static::$version)) return static::$version;

		// check get request - zero is current
		$version = intval(Input::get('a11yc_version', '0'));
		if (static::$version == 0) return static::$version = $version;

		// check db
		// $sql = 'SELECT `version` FROM '.A11YC_TABLE_SETUP.' GROUP BY `version`;';
		// $versions = Db::fetchAll($sql);
		// if (in_array($version, $versions['version']))
		// {
		// 	static::$version = $version;
		// }

		return static::$version;
	}
}
