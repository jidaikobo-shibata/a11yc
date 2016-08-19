<?php
/**
 * Kontiki\Db
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class Db
{
	public static $dbh;

	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// sqlite
		if (strtolower(KONTIKI_DBTYPE) == 'sqlite')
		{
			try
			{
				$dbh = new \PDO("sqlite:".KONTIKI_SQLITE_PATH);
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
			$dtbs = 'mysql:dbname='.KONTIKI_MYSQL_NAME.';host='.KONTIKI_MYSQL_HOST;
			try
			{
				$dbh = new \PDO($dtbs, KONTIKI_MYSQL_USER, KONTIKI_MYSQL_PASSWORD);
			}
			catch (\PDOException $e)
			{
				die('Error:'.$e->getMessage());
			}
			$dbh->query('SET NAMES UTF8;');
		}
		static::$dbh = $dbh;
	}

	/*
	 * init table
	 *
	 * @return  void
	 */
	public static function init_table()
	{
	}

	/**
	 * is_table_exist
	 *
	 * @return  bool
	 */
	public static function is_table_exist($table)
	{
		$table = static::escapeStr($table);
		if(strtolower(KONTIKI_DBTYPE) == 'sqlite')
		{
			$sql = 'PRAGMA table_info('.$table.');';
		}
		elseif(strtolower(KONTIKI_DBTYPE) == 'mysql')
		{
			$sql = 'SHOW COLUMNS FROM '.$table.';';
		}
		return static::fetchAll($sql) ? TRUE :FALSE;
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
			return array_map(array('Kontiki\Db', 'escapeStr'), $str);
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
