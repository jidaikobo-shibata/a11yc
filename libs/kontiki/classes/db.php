<?php
/**
 * Kontiki\Db
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Db
{
	public $dbh;
	protected $name;
	protected $dbtype;
	protected static $_instances = array();

	/**
	 * instance
	 *
	 * @param  String $name
	 * @return Instance
	 */
	public static function instance($name = 'default')
	{
		return array_key_exists($name, static::$_instances) ? static::$_instances[$name] : FALSE;
	}

	/**
	 * Create Db object
	 *
	 * @param  String $name Identifier for this db
	 * @param  Array $cons Configuration
	 * @return Void
	 */
	public static function forge($name = 'default', $cons = array())
	{
		// config
		if (is_array($name))
		{
			$cons = $name;
			$name = 'default';
		}

		// exists
		if ( ! static::instance($name))
		{
			// instance
			static::$_instances[$name] = new static($cons);
		}
	}

	/**
	 * __construct
	 *
	 * @param Array $config
	 * @return Void
	 */
	public function __construct($config)
	{
		$dbtype = strtolower($config['dbtype']);
		// sqlite
		if ($dbtype == 'sqlite')
		{
			try
			{
				$dbh = new \PDO("sqlite:".$config['path']);
				$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch (\PDOException $e)
			{
				Util::error('Connection failed : '.$e->getMessage());
			}
		}
		// mysql
		elseif ($dbtype == 'mysql')
		{
			$dtbs = 'mysql:dbname='.$config['db'].';charset=utf8;host='.$config['host'];
			try
			{
				$dbh = new \PDO($dtbs, $config['user'], $config['password']);
			}
			catch (\PDOException $e)
			{
				Util::error('Error:'.$e->getMessage());
			}
			$dbh->query('SET NAMES UTF8;');
		}

		$this->dbtype = $dbtype;
		$this->dbh = $dbh;
	}

	/**
	 * init table
	 *
	 * @param  String $name
	 * @return void
	 */
	public static function init_table($name = 'default')
	{
	}

	/**
	 * get_fields
	 *
	 * @param  String $table
	 * @param  String $name
	 * @return Array
	 */
	public static function get_fields($table, $name = 'default')
	{
//		$table = ucfirst($table);
		if ( ! static::is_table_exist($table, $name)) return array();
		$instance = static::instance($name);

		if ($instance->dbtype == 'sqlite')
		{
			$sql = "PRAGMA table_info('".$table."');";
			$retvals = self::fetch_all($sql, array(), $name);
			foreach ($retvals as $k => $v)
			{
				if($v['name'])
				{
					$retvals[$k]['name'] = $v['name'];
				}
			}
		}
		elseif ($instance->dbtype == 'mysql')
		{
			$sql = "SHOW COLUMNS FROM ".$table.";";
			$retvals = self::fetch_all($sql, array(), $name);

			$sql2 = "SHOW INDEX FROM ".$table.";";
			$indexes = self::fetch_all($sql2, array(), $name);

			foreach ($retvals as $k => $v)
			{
				if($v['Field'])
				{
					$retvals[$k]['name'] = $v['Field'];

					//search indexes
					foreach ($indexes as $vv)
					{
						if (
							strtolower($v['Field']) == strtolower($vv['Column_name']) &&
							strtolower($v['Key']) != 'pri'
						)
						{
							$retvals[$k]['index_type'] = $vv['Index_type'];
							$retvals[$k]['key_name'] = $vv['Key_name'];
						}
					}
				}
			}
		}

		return $retvals;
	}

	/**
	 * is_table_exist
	 *
	 * @param   string  $table
	 * @param   string  $name
	 * @return  bool
	 */
	public static function is_table_exist($table, $name = 'default')
	{
		$sql = A11YC_DB_TYPE == 'sqlite' ?
				 'select name from sqlite_master where type = "table";' :
				 'show tables;';

		$results = static::fetch_all($sql);
		$tables = array();
		foreach ($results as $row)
		{
			$tables[] = reset($row);
		}
		return in_array($table, $tables);
	}

	/**
	 * is_fields_exist
	 *
	 * @param  String $table
	 * @param  Array $fields
	 * @param  String $name
	 * @return Bool
	 */
	public static function is_fields_exist($table, $fields = array(), $name = 'default')
	{
		if ( ! static::is_table_exist($table, $name)) return false;

		foreach ($fields as $field)
		{
			$retvals[$field] = FALSE;
			foreach (self::get_fields($table, $name) as $exist_fields)
			{
				if ($exist_fields['name'] == $field)
				{
					$retvals[$field] = TRUE;
					break;
				}
			}
		}
		if (in_array(FALSE, $retvals)) return FALSE;
		return TRUE;
	}

	/**
	 * escape
	 *
	 * @param  String|Array $str
	 * @param  String $name
	 * @return String|Array
	 */
	public static function escape($str, $name = 'default')
	{
		if (is_array($str))
		{
			return array_map(array('Kontiki\Db', 'escape'), $str);
		}
		return static::instance($name)->dbh->quote(trim($str));
	}

	/**
	 * fetch one
	 *
	 * @param  String $sql
	 * @param  Array $placeholders
	 * @param  String $name
	 * @return Array
	 */
	public static function fetch
		(
			$sql,
			$placeholders = array(),
			$name = 'default'
		)
	{
		$retval = FALSE;
		$dbh = static::instance($name)->dbh->prepare($sql);
		$dbh->execute($placeholders);
		$retval = $dbh->fetch(\PDO::FETCH_ASSOC);
		$dbh->closeCursor();
		return $retval;
	}

	/**
	 * fetch all
	 *
	 * @param  String $sql
	 * @param  Array $placeholders
	 * @param  String $name
	 * @return Array
	 */
	public static function fetch_all
		(
			$sql,
			$placeholders = array(),
			$name = 'default'
		)
	{
		$dbh = static::instance($name)->dbh->prepare($sql);
		$dbh->execute($placeholders);
		$retvals = $dbh->fetchAll(\PDO::FETCH_ASSOC);
		$dbh->closeCursor();
		return $retvals;
	}

	/**
	 * execute sql
	 *
	 * @param  String $sql
	 * @param  Array $placeholders
	 * @param  String $name
	 * @return Void
	 */
	public static function execute
		(
			$sql,
			$placeholders = array(),
			$name = 'default'
		)
	{
		$dbh = static::instance($name)->dbh->prepare($sql);
		$ret = $dbh->execute($placeholders);
		$dbh->closeCursor();
		return $ret;
	}
}
