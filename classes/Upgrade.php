<?php
/**
 * \JwpA11y\Upgrade
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Upgrade
{
	/**
	 * upgrade
	 *
	 * @return void
	 */
	public static function upgrade()
	{
//		static::mysql2sqlite();
		static::sqlite2mysql();
	}

	/**
	 * sqlite2mysql
	 *
	 * @return void
	 */
	private static function sqlite2mysql()
	{
		if ( ! file_exists(A11YC_DATA_PATH.A11YC_DATA_FILE)) return;

		// 現在のDBにsetupが一行あれば移設は行わない
		$sql = 'SELECT count(*) as row FROM '.A11YC_TABLE_SETUP.';';
		$results = \A11yc\Db::fetch($sql);
		if ($results['row'] != 0) return;

		// forge MySQL
		\A11yc\Db::forge(
			'mysql',
			array(
				'dbtype'   => 'mysql',
				'db'       => DB_NAME,
				'user'     => DB_USER,
				'host'     => DB_HOST,
				'password' => DB_PASSWORD,
			));

		// forge sqlite
		\A11yc\Db::forge(
			'sqlite',
			array(
				'dbtype' => 'sqlite',
				'path' => A11YC_DATA_PATH.A11YC_DATA_FILE,
			));

		// import
		$tables = array(
			A11YC_TABLE_SETUP,
			A11YC_TABLE_PAGES,
			A11YC_TABLE_CHECKS,
			A11YC_TABLE_CHECKS_NGS,
			A11YC_TABLE_BULK,
			A11YC_TABLE_BULK_NGS,
		);

		foreach ($tables as $table)
		{
			// import
			$records = \A11yc\Db::fetch_all('SELECT * FROM '.$table.';', array(), 'sqlite');
			static::simple_import($table, $records, 'mysql');
		}
		// drop table
//		unlink(A11YC_DATA_PATH.A11YC_DATA_FILE);
//		unlink(A11YC_DATA_PATH);
	}

	/**
	 * mysql2sqlite
	 *
	 * @return void
	 */
	private static function mysql2sqlite()
	{
		// do nothing
		if (version_compare(A11YC_VERSION, '0.9.7') == 1) return;
//		if (file_exists(A11YC_DATA_PATH.A11YC_DATA_FILE)) return;

		// db dir
		if (file_exists(A11YC_DATA_PATH)) return;
		mkdir(A11YC_DATA_PATH);

		// forge MySQL
		\A11yc\Db::forge(
			'mysql',
			array(
				'dbtype'   => 'mysql',
				'db'       => DB_NAME,
				'user'     => DB_USER,
				'host'     => DB_HOST,
				'password' => DB_PASSWORD,
			));

		// forge sqlite
		\A11yc\Db::forge(
			'sqlite',
			array(
				'dbtype' => 'sqlite',
				'path' => A11YC_DATA_PATH.A11YC_DATA_FILE,
			));
		\A11yc\Db::init_table('sqlite');

		// import
		$tables = array(
			A11YC_TABLE_SETUP,
			A11YC_TABLE_PAGES,
			A11YC_TABLE_CHECKS,
			A11YC_TABLE_CHECKS_NGS,
			A11YC_TABLE_BULK,
			A11YC_TABLE_BULK_NGS,
		);

		foreach ($tables as $table)
		{
			// import
			$records = \A11yc\Db::fetch_all('SELECT * FROM '.$table.';', array(), 'mysql');
			static::simple_import($table, $records, 'sqlite');

			// drop table
			\A11yc\Db::execute('DROP TABLE '.$table.';', array(), 'mysql');
		}
	}

	/**
	 * simple_import
	 *
	 * @param string $table
	 * @param array $records
	 * @param string $db [mysql,sqlite]
	 * @return void
	 */
	private static function simple_import($table, $records, $db = 'mysql')
	{
		// delete all
		$sql = 'DELETE FROM '.$table.' WHERE 1=1;';
		\A11yc\Db::execute($sql);

		// records loop
		foreach ($records as $datas)
		{
			// add new columns
			if (strpos($table, 'bulk') === false && ! isset($datas['version']))
			{
				$datas['version'] = 0;
			}
			if (strpos($table, 'setup') !== false && ! isset($datas['stop_guzzle']))
			{
				$datas['stop_guzzle'] = 0;
			}

			// prepare
			$fields = array();
			$placeholders = array();
			$vals = array();
			foreach ($datas as $field => $data)
			{
				$fields[] = '`'.$field.'`';
				$placeholders[] = '?';
				$vals[] = $data;
			}

			// sql
			$sql = 'INSERT INTO '.$table.' (';
			$sql.= join(', ', $fields).') VALUES (';
			$sql.= join(', ', $placeholders).');';

			\A11yc\Db::execute($sql, $vals, $db);
		}
	}
}
