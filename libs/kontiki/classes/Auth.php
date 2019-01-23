<?php
/**
 * Kontiki\Auth
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Auth
{
	public static $user_id;

	/**
	 * forge
	 * start session
	 *
	 * @param String $session_name
	 * @return Void
	 */
	public static function forge($session_name = 'KNTKSESSID')
	{
		if ( ! Session::isStarted())
		{
			Session::forge($session_name);
		}
	}

	/**
	 * auth
	 *
	 * @return Bool
	 */
	public static function auth ()
	{
		// check is already logged in?
		if (Session::show('auth', 'uid')) return TRUE;

		// is post exists?
		$username = Input::post('username', false);
		$password = Input::post('password', false);

		if ($username === false || $password === false) return false;
		$users = Users::fetchUsers();
		foreach ($users as $id => $v)
		{
			if ($v[0] === $username && static::verify($password, $v[1]))
			{
				Session::add('auth', 'uid', $id);
				static::$user_id = $id;
				return TRUE;
			}
		}

		// login failed
		return FALSE;
	}

	/**
	 * logout
	 *
	 * @return Void
	 */
	public static function logout ()
	{
		Session::destroy();
	}

	/**
	 * hash
	 *
	 * @param String $str
	 * @return String
	 */
	public static function hash ($str)
	{
		return password_hash($str, CRYPT_BLOWFISH);
	}

	/**
	 * verify
	 *
	 * @param String|Array $password
	 * @param String $hash
	 * @return Bool
	 */
	public static function verify ($password, $hash)
	{
		if ( ! is_string($password)) return false;
		return password_verify($password, $hash);
	}
}
