<?php
/**
 * A11yc\Db
 *
 * @package    part of A11yc
 * @version    1.0
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
	 * @param   string $name
	 * @return  void
	 */
	public static function init_table($name = 'default')
	{
		// create table
		if (defined('A11YC_TABLE_PAGES'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_PAGES))
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
				static::execute($sql);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_PAGES, array('selection_reason')))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `selection_reason` INTEGER;';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_CHECKS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_CHECKS))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS.' (';
				$sql.= '`url`  text NOT NULL,';
				$sql.= '`code` text NOT NULL,';
				$sql.= '`uid`  INTEGER NOT NULL,';
				$sql.= '`memo` text NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}

			// version 1.2.0
			if ( ! static::is_fields_exist(A11YC_TABLE_CHECKS, array('passed')))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS.' ADD `passed` INTEGER;';
				static::execute($sql);

				// update database structure
				$sql = 'UPDATE '.A11YC_TABLE_CHECKS.' SET `passed` = 1;';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_CHECKS_NGS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_CHECKS_NGS))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS_NGS.' (';
				$sql.= '`url`       text NOT NULL,';
				$sql.= '`criterion` text NOT NULL,';
				$sql.= '`uid`       INTEGER NOT NULL,';
				$sql.= '`memo`      text NOT NULL';
				$sql.= ');';
				static::execute($sql);

				// update database structure
				$yml = Yaml::fetch();
				foreach (array_keys($yml['criterions']) as $criterion)
				{
					foreach (Db::fetch_all('SELECT url FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1') as $url)
					{
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS_NGS.' (`url`, `criterion`, `uid`, `memo`)  VALUES (?, ?, ?, ?);';
						static::execute($sql, array($url['url'], $criterion, '1', ''));
					}
				}
			}
		}

		if (defined('A11YC_TABLE_BULK'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_BULK))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_BULK.' (';
				$sql.= '`code` text NOT NULL,';
				$sql.= '`uid`  INTEGER NOT NULL,';
				$sql.= '`memo` text NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_BULK_NGS'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_BULK_NGS))
			{
				$sql = 'CREATE TABLE '.A11YC_TABLE_BULK_NGS.' (';
				$sql.= '`criterion` text NOT NULL,';
				$sql.= '`uid`       INTEGER NOT NULL,';
				$sql.= '`memo`      text NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}
		}

		if (defined('A11YC_TABLE_SETUP'))
		{
			if ( ! static::is_table_exist(A11YC_TABLE_SETUP))
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
				$sql.= '`trust_ssl_url`       text NOT NULL,';
				$sql.= '`checklist_behaviour` INTEGER NOT NULL';
				$sql.= ');';
				static::execute($sql);
			}

			if ( ! static::is_fields_exist(A11YC_TABLE_SETUP, array('additional_criterions')))
			{
				$sql = 'ALTER TABLE '.A11YC_TABLE_SETUP.' ADD `additional_criterions` text;';
				static::execute($sql);
			}
		}
	}
}