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
	 * @param String $session_name
	 * @return void
	 */
	public static function forge($session_name = 'KNTKSESSID')
	{
		// SESSION disabled
		$is_session_disabled = false;
		if (defined('PHP_SESSION_DISABLED') && version_compare(phpversion(), '5.4.0', '>='))
		{
			$is_session_disabled = session_status() === PHP_SESSION_DISABLED ? TRUE : FALSE;
		}
		if ($is_session_disabled)
		{
			Util::error('couldn\'t start session.');
		}

		// SESSION start
		if (static::isStarted() === FALSE && ! headers_sent())
		{
			if (Util::isSsl())
			{
				ini_set('session.cookie_secure', 1);
			}
			ini_set('session.cookie_httponly', true);
			ini_set('session.use_trans_sid', 0);
			ini_set('session.use_only_cookies', 1);
			session_name($session_name);
			session_start();

			// keep security but avoid session down
			static::add('kntk_sess', 'expire', time());
			if(mt_rand(1, 10) === 1)
			{
				$expires = static::show('kntk_sess', 'expire');
				if (end($expires) + 5 < time())
				{
					static::add('kntk_sess', 'expire', time());
					session_regenerate_id(true);
				}
			}
		}
	}

	/**
	 * started?
	 *
	 * @return Bool
	 */
	public static function isStarted()
	{
		if (version_compare(phpversion(), '5.4.0', '>='))
		{
			return session_status() === PHP_SESSION_ACTIVE;
		}
		return session_id() !== '';
	}

	/**
	 * Destroy Session
	 *
	 * @return Void
	 */
	public static function destroy()
	{
		$_SESSION = array();
		if (Input::cookie(session_name()) !== null)
		{
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	/**
	 * add
	 *
	 * @param String $realm
	 * @param String $key
	 * @param Mixed $vals
	 * @return Void
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
	 * @param String $realm
	 * @param String $key
	 * @param Integer $c_key
	 * @return Void
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
	 * @param String $realm
	 * @param String $key
	 * @param Bool $is_once
	 * @return Mixed
	 */
	public static function fetch($realm, $key = '', $is_once = true)
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
				static::$values[$realm][$key] = empty(static::$values[$realm][$key]) ? array() : static::$values[$realm][$key];
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
	 * @param String $realm
	 * @param String $key
	 * @return Mixed
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
