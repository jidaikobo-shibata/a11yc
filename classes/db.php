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
class Db
{
	private static $dbh;

	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// sqlite
		if (strtolower(A11YC_DBTYPE) == 'sqlite')
		{
			try
			{
				$dbh = new \PDO("sqlite:".A11YC_SQLITE_PATH);
				$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch (\PDOException $e)
			{
				die ('Connection failed : '.$e->getMessage());
			}
		}
		// mysql
		else
		{
			$dtbs = 'mysql:dbname='.A11YC_MYSQL_NAME.';host='.A11YC_MYSQL_HOST;
			try
			{
				$dbh = new \PDO($dtbs, A11YC_MYSQL_USER, A11YC_MYSQL_PASSWORD);
			}
			catch (\PDOException $e)
			{
				die('Error:'.$e->getMessage());
			}
			$dbh->query('SET NAMES UTF8;');
		}
		static::$dbh = $dbh;
	}

	/**
	 * is_table_exist
	 *
	 * @return  bool
	 */
	public static function is_table_exist($table)
	{
		$table = static::escapeStr($table);
		if(strtolower(A11YC_DBTYPE) == 'sqlite')
		{
			$sql = 'PRAGMA table_info('.$table.');';
		}
		elseif(strtolower(A11YC_DBTYPE) == 'mysql')
		{
			$sql = 'SHOW COLUMNS FROM '.$table.';';
		}
		return static::fetchAll($sql) ? TRUE :FALSE;
	}

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

	/**
	 * prepare
	 *
	 * @param   string $sql
	 * @return  string
	 */
	public static function prepare($sql)
	{
		$dbh = static::$dbh->prepare($sql);
		if ( ! $dbh)
		{
			// $er = static::$dbh->errorInfo();
			// if($current_user->user_level == 10) echo $er[2];
			die();
		}
		return $dbh;
	}

	/**
	 * escapeStr
	 *
	 * @param   string|array $str
	 * @return  string|array
	 */
	public static function escapeStr($str)
	{
		if (is_array($str))
		{
			return array_map(array('A11yc\Db', 'escapeStr'), $str);
		}
		return static::$dbh->quote(trim($str));
	}

	/**
	 * fetch one.
	 *
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $fetch_style
	 * @return  array
	 */
	public static function fetch($sql, $placeholders = array(), $fetch_style = 'PDO::FETCH_ASSOC')
	{
		$retval = FALSE;
		$fetch_style = substr($fetch_style,0,5) == 'PDO::' ? $fetch_style : 'PDO::'.$fetch_style;
		$dbh = static::prepare($sql);
		$dbh->execute($placeholders);
		$retval = $dbh->fetch(constant($fetch_style));
		$dbh->closeCursor();
		return $retval;
	}

	/**
	 * fetch_all.
	 *
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $fetch_style
	 * @return  array
	 */
	public static function fetchAll($sql, $placeholders = array(), $fetch_style='PDO::FETCH_ASSOC')
	{
		$retvals = array();
		$fetch_style = substr($fetch_style,0,5) == 'PDO::' ? $fetch_style : 'PDO::'.$fetch_style;
		$dbh = static::prepare($sql);
		$dbh->execute($placeholders);
		$retvals = $dbh->fetchAll(constant($fetch_style));
		$dbh->closeCursor();
		return $retvals;
	}

	/**
	 * execute sql.
	 *
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @return  void
	 */
	public static function execute($sql, $placeholders = array())
	{
		$dbh = static::prepare($sql);
		if ($placeholders)
		{
			$dbh->execute($placeholders);
		}
		else
		{
			$dbh->execute();
		}
		$dbh->closeCursor();
	}
}
