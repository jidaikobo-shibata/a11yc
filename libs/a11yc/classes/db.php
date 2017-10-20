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

class Db extends \Kontiki\Db
{
	/**
	 * init table
	 *
	 * @param  String $name
	 * @return Void
	 */
	public static function init_table($name = 'default')
	{
		// return
		if (static::is_fields_exist(A11YC_TABLE_SETUP, array('version'), $name)) return;

		// base tables
		static::init_pages($name);
		static::init_checks($name);
		static::init_bulk($name);
		static::init_setup($name);
		static::init_maintenance($name);
	}

	/**
	 * init pages table
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function init_pages($name = 'default')
	{
		if (defined('A11YC_TABLE_PAGES'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_PAGES, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_PAGES.' (';
				$sql.= '`url`        text NOT NULL,';
				$sql.= '`standard`   INTEGER,';
				$sql.= '`level`      INTEGER,';
				$sql.= '`done`       bool,';
				$sql.= '`date`       date,';
				$sql.= '`add_date`   datetime,';
				$sql.= '`page_title` text,';
				$sql.= '`trash`      bool NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_PAGES, array('selection_reason'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `selection_reason` INTEGER;';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_PAGES, array('version'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `version` INTEGER DEFAULT 0;';
				static::execute($sql, array(), $name);
			}
		}
	}

	/**
	 * init checks table
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function init_checks($name = 'default')
	{
		if (defined('A11YC_TABLE_CHECKS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_CHECKS, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS.' (';
				$sql.= '`url`  text NOT NULL,';
				$sql.= '`code` text NOT NULL,';
				$sql.= '`uid`  INTEGER NOT NULL,';
				$sql.= '`memo` text NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);
			}

			// switch to passed flag
			if ( ! static::is_fields_exist(A11YC_TABLE_CHECKS, array('passed'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS.' ADD `passed` INTEGER;';
				static::execute($sql, array(), $name);

				// update database structure
				$sql = 'UPDATE '.A11YC_TABLE_CHECKS.' SET `passed` = 1;';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_CHECKS, array('version'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS.' ADD `version` INTEGER DEFAULT 0;';
				static::execute($sql, array(), $name);
			}
		}

		if (defined('A11YC_TABLE_CHECKS_NGS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_CHECKS_NGS, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS_NGS.' (';
				$sql.= '`url`       text NOT NULL,';
				$sql.= '`criterion` text NOT NULL,';
				$sql.= '`uid`       INTEGER NOT NULL,';
				$sql.= '`memo`      text NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);

				// update database structure
				$yml = Yaml::fetch();
				foreach (array_keys($yml['criterions']) as $criterion)
				{
					$sql = 'SELECT url FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1';
					foreach (Db::fetch_all($sql, array(), $name) as $url)
					{
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS_NGS;
						$sql.= ' (`url`, `criterion`, `uid`, `memo`) VALUES (?, ?, ?, ?);';
						static::execute($sql, array($url['url'], $criterion, '1', ''), $name);
					}
				}
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_CHECKS_NGS, array('version'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS_NGS.' ADD `version` INTEGER DEFAULT 0;';
				static::execute($sql, array(), $name);
			}
		}
	}

	/**
	 * init bulk table
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function init_bulk($name = 'default')
	{
		if (defined('A11YC_TABLE_BULK'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_BULK, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_BULK.' (';
				$sql.= '`code` text NOT NULL,';
				$sql.= '`uid`  INTEGER NOT NULL,';
				$sql.= '`memo` text NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);
			}
		}

		if (defined('A11YC_TABLE_BULK_NGS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_BULK_NGS, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_BULK_NGS.' (';
				$sql.= '`criterion` text NOT NULL,';
				$sql.= '`uid`       INTEGER NOT NULL,';
				$sql.= '`memo`      text NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);
			}
		}
	}

	/**
	 * init setup table
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function init_setup($name = 'default')
	{
		if (defined('A11YC_TABLE_SETUP'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_SETUP, $name))
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
				$sql.= '`basic_user`          text NOT NULL,';
				$sql.= '`basic_pass`          text NOT NULL,';
				$sql.= '`trust_ssl_url`       text NOT NULL,'; // this is unused column. but unfortunately SQLITE cannot drop column.
				$sql.= '`checklist_behaviour` INTEGER NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_SETUP, array('additional_criterions'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_SETUP.' ADD `additional_criterions` text;';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_SETUP, array('stop_guzzle'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_SETUP.' ADD `stop_guzzle` INTEGER NOT NULL DEFAULT 0;';
				static::execute($sql, array(), $name);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_SETUP, array('version'), $name))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_SETUP.' ADD `version` INTEGER DEFAULT 0;';
				static::execute($sql, array(), $name);
			}
		}
	}

	/**
	 * init maintenance table
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function init_maintenance($name = 'default')
	{
		if (defined('A11YC_TABLE_MAINTENANCE'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_MAINTENANCE, $name))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_MAINTENANCE.' (';
				$sql.= '`last_checked` date,';
				$sql.= '`version` TEXT NOT NULL';
				$sql.= ');';
				static::execute($sql, array(), $name);

				// default value
				$sql = 'INSERT INTO '.A11YC_TABLE_MAINTENANCE;
				$sql.= ' (`last_checked`, `version`) VALUES';
				$sql.= '("'.date('Y-m-d').'", "'.A11YC_VERSION.'");';
				static::execute($sql, array(), $name);
			}
		}
	}

}
