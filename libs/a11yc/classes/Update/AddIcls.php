<?php
/**
 * A11yc\Update\AddIcls
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class AddIcls
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		self::issues();
		self::pages();
		self::icls();
	}

	/**
	 * issues
	 *
	 * @return Void
	 */
	private static function issues()
	{
		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `image_path` TEXT DEFAULT "";';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `title` TEXT DEFAULT "";';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `seq` INTEGER DEFAULT 0;';
		Db::execute($sql);
	}

	/**
	 * pages
	 *
	 * @return Void
	 */
	private static function pages()
	{
		$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `image_path` TEXT DEFAULT "";';
		Db::execute($sql);
	}

	/**
	 * icls
	 *
	 * @return Void
	 */
	private static function icls()
	{
		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';

		// init implement table
		$sql = 'CREATE TABLE '.A11YC_TABLE_ICLS.' (';
		$sql.= '`title`      TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`inspection` TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`type`       TEXT CHARACTER'.$set_utf8.','; // AC/AF/HC
		$sql.= '`implements` TEXT CHARACTER'.$set_utf8.','; // serialized value
		$sql.= '`criterion`  VARCHAR(10) NOT NULL,';
		$sql.= '`identifier` VARCHAR(10) NOT NULL,';
		$sql.= '`criterions` VARCHAR(10) NOT NULL,';
		$sql.= '`situation`  INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`seq`        INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`version`    INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		Db::execute($sql, array());

		// init situations
		$auto_increment = A11YC_DB_TYPE == 'mysql' ? 'auto_increment' : '' ;
		$sql = 'CREATE TABLE '.A11YC_TABLE_ICLSSIT.' (';
		$sql.= '`id`        INTEGER NOT NULL PRIMARY KEY '.$auto_increment.',';
		$sql.= '`criterion` VARCHAR(10) NOT NULL,';
		$sql.= '`value`     TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`version`   INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		Db::execute($sql, array());

		// alter table results - serialized values
		$sql = 'ALTER TABLE '.A11YC_TABLE_RESULTS.' ADD `implements` TEXT DEFAULT "";';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_BRESULTS.' ADD `implements` TEXT DEFAULT "";';
		Db::execute($sql);
	}
}
