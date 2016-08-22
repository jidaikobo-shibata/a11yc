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
	protected static $dbh;
	protected static $configs = array();
	protected static $_instance = array();
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
		}
		else
		{
			$tmp = include KONTIKI_CONFIG_PATH.'/kontiki.php';
			static::$configs = $tmp['db'];
			if ( ! isset(static::$configs[$name]))
			{
				die('databse setting is not found');
			}
		}
		$config = static::$configs[$name];

		// instance
		static::$_instances[$name] = new static($name, $config);

		if ($name == 'default')
		{
			static::$_instance = static::$_instances[$name];
		}

		return static::$_instances[$name];
	}

	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct($name, $config)
	{
		// sqlite
		if (strtolower($config['dbtype']) == 'sqlite')
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
		else
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
	public static function is_table_exist($table, $name = 'default')
	{
		$table = static::escapeStr($table, $name);
		if(strtolower(static::$configs[$name]['dbtype']) == 'sqlite')
		{
			$sql = 'PRAGMA table_info('.$table.');';
		}
		elseif(strtolower(static::$configs[$name]['dbtype']) == 'mysql')
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
	public static function escapeStr($str, $name = 'default')
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
	public static function fetch($sql, $placeholders = array(), $fetch_style = 'PDO::FETCH_ASSOC', $name = 'default')
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
	 * fetch_all
	 *
	 * @param   string     $sql
	 * @param   array      $placeholders
	 * @param   string     $fetch_style
	 * @return  array
	 */
	public function fetch_all($sql, $name = 'default')
	{
		$instance = static::instance($name);


		$retvals = array();
		$fetch_style = substr($fetch_style,0,5) == 'PDO::' ? $fetch_style : 'PDO::'.$fetch_style;
		$dbh = static::prepare($sql);
		$dbh->execute($placeholders);
		$retvals = $dbh->fetchAll(constant($fetch_style));
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
