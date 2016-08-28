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
	protected static $configs = array();
	protected static $_instances = array();

	/**
	 * Create Fieldset object
	 *
	 * @param   string    $name
	 * @return  instance
	 */
	public static function instance($name = 'default')
	{
		return isset(static::$_instances[$name]) ? static::$_instances[$name] : FALSE;
	}

	/**
	 * Create Db object
	 *
	 * @param   string    Identifier for this db
	 * @param   array     Configuration array
	 * @return  Fieldset
	 */
	public static function forge($name = 'default')
	{
		// exists
		if (static::instance($name)) die('already exists');

		// config
		if (is_array($name))
		{
			static::$configs['default'] = $name;
			$name = 'default';
		}
		else
		{
			$config = include KONTIKI_CONFIG_PATH;
			static::$configs = $config['db'];
			if ( ! isset(static::$configs[$name]))
			{
				die('databse setting is not found');
			}
		}
		$config = static::$configs[$name];

		// instance
		static::$_instances[$name] = new static($name, $config);

		// TODO: recognize instance eventually
		// return static::$_instances[$name];
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
			$dtbs = 'mysql:dbname='.$config['db'].';host='.$config['host'];
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
	 * is_table_exist
	 *
	 * @param   string  $table
	 * @param   string  $name
	 * @return  bool
	 */
	public static function is_table_exist($table, $name = 'default')
	{
		$table = static::escapeStr($table, $name);
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
	 * escapeStr
	 *
	 * @param   string|array $str
	 * @param   string       $name
	 * @return  string|array
	 */
	public static function escapeStr($str, $name = 'default')
	{
		if (is_array($str))
		{
			return array_map(array('Kontiki\Db', 'escapeStr'), $str);
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
	public static function fetch($sql, $placeholders = array(), $name = 'default')
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
	public static function fetch_all($sql, $placeholders = array(), $name = 'default')
	{
		$dbh = static::instance($name)->dbh->prepare($sql);
		$dbh->execute($placeholders);
		$retvals = $dbh->fetchAll(\PDO::FETCH_ASSOC);
		$dbh->closeCursor();
		return $retvals;
	}

	/**
	 * fetch_all.
	 *
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $fetch_style
	 * @return  array
	 */
	public static function fetchAll($sql, $placeholders = array(), $fetch_style='PDO::FETCH_ASSOC', $name = 'default')
	{
		$instance = static::instance($name);
		$fetch_style = substr($fetch_style,0,5) == 'PDO::' ? $fetch_style : 'PDO::'.$fetch_style;
		$dbh = $instance->prepare($sql, $name);
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
	 * @param   string     $name
	 * @return  void
	 */
	public static function execute($sql, $placeholders = array(), $name = 'default')
	{
		$dbh = static::instance($name)->dbh->prepare($sql);
		$dbh->execute($placeholders);
		$dbh->closeCursor();
	}
}
