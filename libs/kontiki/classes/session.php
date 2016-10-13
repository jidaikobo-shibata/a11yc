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
		else if (session_status() === PHP_SESSION_NONE)
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
	 * add
	 *
	 * @param   string    $realm
	 * @param   string    $key
	 * @param   array     $vals
	 * @return  void
	 */
	public static function add($realm, $key, $vals)
	{
		static::$values[$realm][$key][] = $vals;
		static::$values[$realm][$key] = array_unique(static::$values[$realm][$key]);
		$_SESSION[$realm] = static::$values[$realm];
	}

	/**
	 * remove
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @param   int     $child_key
	 * @return  void
	 */
	public static function remove($realm, $key = '', $child_key = '')
	{
		// remove realm
		if (empty($key) && empty($child_key) && isset(static::$values[$realm]))
		{
			unset(static::$values[$realm]);
			unset($_SESSION[$realm]);
		}
		// remove key
		elseif(empty($child_key) && isset(static::$values[$realm][$key]))
		{
			unset(static::$values[$realm][$key]);
			unset($_SESSION[$realm][$key]);
		}
		// remove key
		elseif(isset(static::$values[$realm][$key][$child_key]))
		{
			unset(static::$values[$realm][$key][$child_key]);
			unset($_SESSION[$realm][$key][$child_key]);
		}
	}

	/**
	 * fetch
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @param   bool    $is_once
	 * @return  void
	 */
	public static function fetch($realm, $key = '', $is_once = 1)
	{
		$vals = array();
		if (empty($key))
		{
			if (isset($_SESSION[$realm]))
			{
				$vals = $_SESSION[$realm];
				if ($is_once) unset($_SESSION[$realm]);
			}
			if (isset(static::$values[$realm]))
			{
				$vals = array_merge($vals, static::$values[$realm]);
			}
		}
		elseif (
			isset(static::$values[$realm][$key]) ||
			isset($_SESSION[$realm][$key])
		)
		{
			if (isset($_SESSION[$realm][$key]))
			{
				$vals = $_SESSION[$realm][$key];
				if ($is_once) unset($_SESSION[$realm][$key]);
			}
			if (isset(static::$values[$realm]))
			{
				$vals = array_merge($vals, static::$values[$realm][$key]);
			}
		}
		$vals = array_unique($vals);
		return $vals;
	}

	/**
	 * show
	 *
	 * @param   string  $realm
	 * @param   string  $key
	 * @return  void
	 */
	public static function show($realm = '', $key = '')
	{
		if (empty($realm))
		{
			return array_merge(static::$values, $_SESSION);
		}
		return static::fetch($realm, $key, false);
	}
}
