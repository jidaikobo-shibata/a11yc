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
	protected static $version = null;

	/**
	 * init table
	 *
	 * @param  String $name
	 * @return Void
	 */
	public static function initTable($name = 'default')
	{
		// init default tables
		if (static::isTableExist(A11YC_TABLE_CACHES, $name)) return;
		static::initDefault($name);
	}

	/**
	 * init tables
	 *
	 * @param  String $name
	 * @return Void
	 */
	private static function initDefault($name = 'default')
	{
		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';

		// init pages
		// type: 1 HTML, 2 PDF
		// level: -1 Non-Interferences, 0 A-, 1 A, 2 AA, 3 AAA
		$sql = 'CREATE TABLE '.A11YC_TABLE_PAGES.' (';
		$sql.= '`url`              VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`alt_url`          VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`type`             INTEGER NOT NULL DEFAULT 1,';
		$sql.= '`title`            TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`level`            INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`standard`         INTEGER NOT NULL DEFAULT 1,';
		$sql.= '`selection_reason` INTEGER NOT NULL DEFAULT 1,';
		$sql.= '`done`             BOOL NOT NULL DEFAULT 0,';
		$sql.= '`trash`            BOOL NOT NULL DEFAULT 0,';
		$sql.= '`date`             DATE,';
		$sql.= '`created_at`       DATETIME,';
		$sql.= '`updated_at`       DATETIME,';
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

		// init ua
		$auto_increment = A11YC_DB_TYPE == 'mysql' ? 'auto_increment' : '' ;
		$sql = 'CREATE TABLE '.A11YC_TABLE_UAS.' (';
		$sql.= '`id`   INTEGER NOT NULL PRIMARY KEY '.$auto_increment.',';
		$sql.= '`name` TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`str`  TEXT CHARACTER'.$set_utf8.'';
		$sql.= ');';
		static::execute($sql, array(), $name);

		// add default ua
		foreach (Values::uas() as $v)
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_UAS;
			$sql.= ' (`name`, `str`) VALUES (?, ?);';
			static::execute($sql, array($v['name'], $v['str']), $name);
		}

		// init cache
		// type: raw, high-lighted, ignored, ignored_comments or something
		// don't use page_id because one time using.
		// don't use ua_id because ua can be deleted
		$sql = 'CREATE TABLE '.A11YC_TABLE_CACHES.' (';
		$sql.= '`url`        VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`ua`         VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`type`       VARCHAR(20) NOT NULL,';
		$sql.= '`data`       TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`updated_at` DATETIME';
		$sql.= ');';
		static::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_CACHES;
			$sql.= ' ADD INDEX a11yc_caches_idx(`url`, `type`, `ua`)';
			static::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_caches_idx ON '.A11YC_TABLE_CACHES;
			$sql.= ' (`url`, `type`, `ua`)';
			static::execute($sql, array(), $name);
		}

		// init version status
		$sql = 'CREATE TABLE '.A11YC_TABLE_VERSIONS.' (';
		$sql.= '`version` INTEGER NOT NULL PRIMARY KEY,';
		$sql.= '`name`    TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`trash`   INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		// init checks
		$sql = 'CREATE TABLE '.A11YC_TABLE_CHECKS.' (';
		$sql.= '`url`        VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`code`       VARCHAR(10) NOT NULL,';
		$sql.= '`is_checked` BOOL NOT NULL DEFAULT 0,';
		$sql.= '`is_failure` BOOL NOT NULL DEFAULT 0,';
		$sql.= '`version`    INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS.' ADD INDEX a11yc_checks_idx(`url`, `version`)';
			static::execute($sql, array(), $name);

			$sql = 'ALTER TABLE '.A11YC_TABLE_CHECKS.' ADD INDEX a11yc_checks_idx2(`url`, `code`, `version`)';
			static::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_checks_idx ON '.A11YC_TABLE_CHECKS.' (`url`, `version`)';
			static::execute($sql, array(), $name);

			$sql = 'CREATE INDEX a11yc_checks_idx2 ON '.A11YC_TABLE_CHECKS.' (`url`, `code`, `version`)';
			static::execute($sql, array(), $name);
		}

		// init results
		// result: 0 not yet, -1 not passed, 1 non-exist and passed, 2 passed
		// method: 0 not yet, 1 AC, 2 AF, 3 HC
		$sql = 'CREATE TABLE '.A11YC_TABLE_RESULTS.' (';
		$sql.= '`url`       VARCHAR(2048) NOT NULL DEFAULT "",';
		$sql.= '`criterion` VARCHAR(10) NOT NULL,';
		$sql.= '`memo`      TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`uid`       INTEGER NOT NULL,';
		$sql.= '`result`    INTEGER NOT NULL,';
		$sql.= '`method`    INTEGER NOT NULL,';
		$sql.= '`version`   INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_RESULTS.' ADD INDEX a11yc_results_idx(`url`, `version`)';
			static::execute($sql, array(), $name);

			$sql = 'ALTER TABLE '.A11YC_TABLE_RESULTS.' ADD INDEX a11yc_results_idx2(`criterion`, `url`, `version`)';
			static::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_results_idx ON '.A11YC_TABLE_RESULTS.' (`url`, `version`)';
			static::execute($sql, array(), $name);

			$sql = 'CREATE INDEX a11yc_results_idx2 ON '.A11YC_TABLE_RESULTS.' (`criterion`, `url`, `version`)';
			static::execute($sql, array(), $name);
		}

		// init bulk results
		$sql = 'CREATE TABLE '.A11YC_TABLE_BRESULTS.' (';
		$sql.= '`criterion` VARCHAR(10) NOT NULL,';
		$sql.= '`memo`      TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`uid`       INTEGER NOT NULL,';
		$sql.= '`result`    INTEGER NOT NULL,';
		$sql.= '`method`    INTEGER NOT NULL';
		$sql.= ');';
		static::execute($sql, array(), $name);

		// init bchecks
		$sql = 'CREATE TABLE '.A11YC_TABLE_BCHECKS.' (';
		$sql.= '`code`       VARCHAR(10) NOT NULL,';
		$sql.= '`is_checked` BOOL NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		// init issues
		// status: 0 not yet, 1 in progress, 2 finish
		// n_or_e: 0 notice, 1 error
		$sql = 'CREATE TABLE '.A11YC_TABLE_ISSUES.' (';
		$sql.= '`id`            INTEGER NOT NULL PRIMARY KEY '.$auto_increment.',';
		$sql.= '`is_common`     BOOL NOT NULL DEFAULT 0,';
		$sql.= '`url`           TEXT NOT NULL DEFAULT "",';
		$sql.= '`criterion`     VARCHAR(10) NOT NULL,';
		$sql.= '`html`          TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`n_or_e`        INTEGER NOT NULL DEFAULT 0,'; // notice or error
		$sql.= '`status`        INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`tech_url`      VARCHAR(255) NOT NULL,'; // some unique strings
		$sql.= '`error_message` TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`created_at`    DATETIME,';
		$sql.= '`uid`           INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`version`       INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);

		if (A11YC_DB_TYPE == 'mysql')
		{
			$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD INDEX a11yc_issues_idx(`url`, `version`)';
			static::execute($sql, array(), $name);
		}
		else
		{
			$sql = 'CREATE INDEX a11yc_issues_idx ON '.A11YC_TABLE_ISSUES.' (`url`, `version`)';
			static::execute($sql, array(), $name);
		}

		// init issues bbs
		$sql = 'CREATE TABLE '.A11YC_TABLE_ISSUESBBS.' (';
		$sql.= '`id`         INTEGER NOT NULL PRIMARY KEY '.$auto_increment.',';
		$sql.= '`issue_id`   INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`uid`        INTEGER NOT NULL DEFAULT 0,';
		$sql.= '`created_at` DATETIME,';
		$sql.= '`message`    TEXT CHARACTER'.$set_utf8.'';
		$sql.= ');';
		static::execute($sql, array(), $name);

		// init setups
		self::createSettings($name);

		// init maintenance
		$sql = 'CREATE TABLE '.A11YC_TABLE_MAINTENANCE.' (';
		$sql.= '`last_checked` DATE';
		$sql.= ');';
		static::execute($sql, array(), $name);

		$sql = 'INSERT INTO '.A11YC_TABLE_MAINTENANCE;
		$sql.= ' (`last_checked`) VALUES (?);';
		static::execute($sql, array(date('Y-m-d')), $name);
	}

	/**
	 * createSettings
	 * public because call from Update\updateSettings
	 *
	 * @return Void
	 */
	public static function createSettings($name)
	{
		$set_utf8 = A11YC_DB_TYPE == 'mysql' ? ' SET utf8' : '';
		// init setups
		$sql = 'CREATE TABLE '.A11YC_TABLE_SETTINGS.' (';
		$sql.= '`key`     TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`value`   TEXT CHARACTER'.$set_utf8.',';
		$sql.= '`version` INTEGER NOT NULL DEFAULT 0';
		$sql.= ');';
		static::execute($sql, array(), $name);
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
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_SETUP.' GROUP BY `version`;';
		$versions = Db::fetchAll($sql);
		if (in_array($version, $versions['version']))
		{
			static::$version = $version;
		}

		return static::$version;
	}
}
