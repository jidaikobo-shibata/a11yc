<?php
/**
 * A11yc\Update\AddImplement
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class AddImplement
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
return;

		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';

		// init implement table
		$sql = 'CREATE TABLE '.A11YC_TABLE_IMPLEMENT.' (';
		$sql.= '`title`            TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`inspection`       TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`type`             TEXT CHARACTER'.$set_utf8.','; // AC/AF/HC
		$sql.= '`criterion`        INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`situation`        INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`seq`              INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`version`          INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD INDEX a11yc_url_idx(`url`, `version`)';
			static::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_url_idx ON '.A11YC_TABLE_PAGES.' (`url`, `version`)';
			static::execute($sql, array(), $name);
		}



		// $sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `image_path` TEXT DEFAULT "";';
		// Db::execute($sql);
	}
}
