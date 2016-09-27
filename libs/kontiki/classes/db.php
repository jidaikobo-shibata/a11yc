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
	protected $dbh;
	protected $name;
	protected $dbtype;
	protected static $_instances = array();

	/**
	 * instance
	 *
	 * @param   string    $name
	 * @return  instance
	 */
	public static function instance($name = 'default')
	{
		return array_key_exists($name, static::$_instances) ? static::$_instances[$name] : FALSE;
	}

	/**
	 * Create Db object
	 *
	 * @param   string    Identifier for this db
	 * @param   array     Configuration array
	 * @return  Fieldset
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
		if (static::instance($name)) die('already exists');

		// instance
		static::$_instances[$name] = new static($name, $cons);
	}

	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct($name, $config)
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
				die ('Connection failed : '.$e->getMessage());
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
				die('Error:'.$e->getMessage());
			}
			$dbh->query('SET NAMES UTF8;');
		}

		$this->dbtype = $dbtype;
		$this->dbh = $dbh;
	}

	/*
	 * init table
	 *
	 * @param   string  $name
	 * @return  void
	 */
	public static function init_table($name = 'default')
	{
	}

	/**
	 * get_fields
	 *
	 * @param   string  $table
	 * @return  array
	 */
	public function get_fields($table)
	{
		$table = ucfirst($table);

		if ($this->dbtype == 'sqlite')
		{
			$sql = "PRAGMA table_info('".$table."');";
			$retvals = self::fetch_all($sql);
			foreach ($retvals as $k => $v)
			{
				if($v['name'])
				{
					$retvals[$k]['name'] = $v['name'];
				}
			}
		}
		elseif ($this->dbtype == 'mysql')
		{
			$sql = "SHOW COLUMNS FROM ".$table.";";
			$retvals = self::fetch_all($sql);

			$sql2 = "SHOW INDEX FROM ".$table.";";
			$indexes = self::fetch_all( $sql2 );

			foreach ($retvals as $k => $v)
			{
				if($v['Field'])
				{
					$retvals[$k]['name'] = $v['Field'];

					//search indexes
					foreach ( $indexes as $kk => $vv )
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
		$table = static::escape($table, $name);
		$instance = static::instance($name);
		if($instance->dbtype == 'sqlite')
		{
			$sql = 'PRAGMA table_info('.$table.');';
		}
		elseif($instance->dbtype == 'mysql')
		{
			$sql = 'SHOW COLUMNS FROM '.$table.';';
		}
		return static::fetch_all($sql) ? TRUE : FALSE;
	}

	/**
	 * is_fields_exist
	 *
	 * @param   string  $table
	 * @param   array   $fields
	 * @return  bool
	 */
	public function is_fields_exist($table, $fields = array())
	{
		foreach ($fields as $field)
		{
			$retvals[$field] = FALSE;
			foreach (self::get_fields($table) as $exist_fields)
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
	 * @param   string|array $str
	 * @param   string       $name
	 * @return  string|array
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
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $name
	 * @return  array
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
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $name
	 * @return  array
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
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $name
	 * @return  void
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
