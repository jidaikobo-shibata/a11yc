<?php
/**
 * Kontiki\Session
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class Session
{
	protected static $values = array();

	/**
	 * _init
	 *
	 * @return  void
	 */
	public static function _init()
	{
		if (session_status() === PHP_SESSION_DISABLED)
		{
			die('couldn\'t start session.');
		}
		else if (session_status() === PHP_SESSION_NONE && ! headers_sent())
		{
			if (Util::is_ssl())
			{
				ini_set('session.cookie_secure', 1);
			}
			ini_set('session.use_trans_sid', 0);
			ini_set('session.use_only_cookies', 1);
			session_name('KNTKSESSID');
			session_start();
			session_regenerate_id(true);
		}
	}

	/**
	 * Create Session
	 *
	 * @return  void
	 */
	public static function forge()
	{
		return true;
	}

	/**
	 * Destroy Session
	 *
	 * @return  void
	 */
	public static function destroy()
	{
		$_SESSION = array();
		if (isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	/**
	 * add
	 *
	 * @param   string    $realm
	 * @param   string    $key
	 * @param   mixed     $vals
	 * @return  void
	 */
	public static function add($realm, $key, $vals)
	{
		static::$values[$realm][$key][] = $vals;
		if (isset($_SESSION[$realm]))
		{
			static::$values[$realm] = array_merge($_SESSION[$realm], static::$values[$realm]);
		}
		static::$values[$realm][$key] = array_unique(static::$values[$realm][$key]);
		$_SESSION[$realm] = static::$values[$realm];
	}

	/**
	 * remove
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @param   int     $c_key
	 * @return  void
	 */
	public static function remove($realm, $key = '', $c_key = '')
	{
		// remove realm
		if (empty($key) && empty($c_key))
		{
			if (isset($_SESSION[$realm]))
			{
				unset($_SESSION[$realm]);
			}
			if (isset(static::$values[$realm]))
			{
				unset(static::$values[$realm]);
			}
		}
		// remove key
		elseif(empty($c_key))
		{
			if (isset($_SESSION[$realm][$key]))
			{
				unset($_SESSION[$realm][$key]);
			}
			if (isset(static::$values[$realm][$key]))
			{
				unset(static::$values[$realm][$key]);
			}
		}
		// remove each value
		else
		{
			if (isset($_SESSION[$realm][$key][$c_key]))
			{
				unset($_SESSION[$realm][$key][$c_key]);
			}
			if (isset(static::$values[$realm][$key][$c_key]))
			{
				unset(static::$values[$realm][$key][$c_key]);
			}
		}
	}

	/**
	 * fetch
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @param   bool    $is_once
	 * @return  mixed
	 */
	public static function fetch($realm, $key = '', $is_once = 1)
	{
		$vals = array();
		if (empty($key))
		{
			if (isset($_SESSION[$realm]))
			{
				$vals = $_SESSION[$realm];
			}
			if (isset(static::$values[$realm]))
			{
				$vals = array_merge($vals, static::$values[$realm]);
			}
			if ($is_once) static::remove($realm);
		}
		elseif (
			isset(static::$values[$realm][$key]) ||
			isset($_SESSION[$realm][$key])
		)
		{
			if (isset($_SESSION[$realm][$key]))
			{
				$vals = $_SESSION[$realm][$key];
			}
			if (isset(static::$values[$realm]))
			{
				$vals = array_merge($vals, static::$values[$realm][$key]);
			}
			if ($is_once) static::remove($realm, $key);
		}
		$vals = array_unique($vals);
		return $vals ?: false;
	}

	/**
	 * show
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @return  mixed
	 */
	public static function show($realm = '', $key = '')
	{
		if (empty($realm))
		{
			return array_merge(static::$values, $_SESSION);
		}
		return static::fetch($realm, $key, false) ?: false;
	}
}
