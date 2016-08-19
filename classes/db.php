<?php
/**
 * A11yc\Db
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Db extends \Kontiki\Db
{
	/**
	 * init table
	 *
	 * @return  void
	 */
	public static function init_table()
	{
		// create table
		if (defined('A11YC_TABLE_PAGES'))
		{
			if( ! static::is_table_exist(A11YC_TABLE_PAGES))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_PAGES.' (';
				$sql.= '`url`      text NOT NULL,';
				$sql.= '`standard` INTEGER,';
				$sql.= '`level`    INTEGER,';
				$sql.= '`done`     bool,';
				$sql.= '`date`     date,';
				$sql.= '`trash`    bool NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_CHECKS'))
		{
			if( ! static::is_table_exist(A11YC_TABLE_CHECKS))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS.' (';
				$sql.= '`url`  text NOT NULL,';
				$sql.= '`code` text NOT NULL,';
				$sql.= '`uid`  INTEGER NOT NULL,';
				$sql.= '`memo` text NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_BULK'))
		{
			if( ! static::is_table_exist(A11YC_TABLE_BULK))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_BULK.' (';
				$sql.= '`code`     text NOT NULL,';
				$sql.= '`uid`      INTEGER NOT NULL,';
				$sql.= '`memo`     text NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_SETUP'))
		{
			if( ! static::is_table_exist(A11YC_TABLE_SETUP))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_SETUP.' (';
				$sql.= '`target_level`        INTEGER NOT NULL,';
				$sql.= '`standard`            INTEGER NOT NULL,';
				$sql.= '`selected_method`     INTEGER NOT NULL,';
				$sql.= '`declare_date`        date NOT NULL,';
				$sql.= '`test_period`         text NOT NULL,';
				$sql.= '`dependencies`        text NOT NULL,';
				$sql.= '`contact`             text NOT NULL,';
				$sql.= '`policy`              text NOT NULL,';
				$sql.= '`report`              text NOT NULL,';
				$sql.= '`checklist_behaviour` INTEGER NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}
	}
}