<?php
/**
 * Kontiki\Session
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Session
{
	protected static $values = array();

	/**
	 * Create Session
	 *
	 * @return  void
	 */
	public static function forge($session_name = 'KNTKSESSID')
	{
		// SESSION disabled
		$is_session_disabled = false;
		if (version_compare(phpversion(), '5.4.0', '>='))
		{
			$is_session_disabled = session_status() === PHP_SESSION_DISABLED ? TRUE : FALSE;
		}
		if ($is_session_disabled)
		{
			Util::error('couldn\'t start session.');
		}

		// SESSION start
		if (static::is_started() === FALSE && ! headers_sent())
		{
			if (Util::is_ssl())
			{
				ini_set('session.cookie_secure', 1);
			}
			ini_set('session.cookie_httponly', true);
			ini_set('session.use_trans_sid', 0);
			ini_set('session.use_only_cookies', 1);
			session_name($session_name);
			session_start();
			session_regenerate_id(true);
		}
	}

	/**
	 * started?
	 *
	 * @return bool
	 */
	public static function is_started()
	{
		$is_session_started = false;
		if (version_compare(phpversion(), '5.4.0', '>='))
		{
			$is_session_started = session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
		}
		else
		{
			$is_session_started = session_id() === '' ? FALSE : TRUE;
		}
		return $is_session_started;
	}

	/**
	 * Destroy Session
	 *
	 * @return  void
	 */
	public static function destroy()
	{
		$_SESSION = array();
		if (Input::cookie(session_name()))
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

		// if key exists, merge.
		if (isset($_SESSION[$realm][$key]))
		{
			static::$values[$realm][$key] = array_merge(
				$_SESSION[$realm][$key],
				static::$values[$realm][$key]
			);
		}
		// realm
		elseif (isset($_SESSION[$realm]))
		{
			static::$values[$realm] = array_merge(
				$_SESSION[$realm],
				static::$values[$realm]
			);
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
	 * fetch data from SESSION and static value.
	 * after fetching data will be deleted (default).
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
