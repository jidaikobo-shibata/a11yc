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
	public static $hash = false;

	/**
	 * _init
	 * start session
	 *
	 * @return  void
	 */
	public static function _init()
	{
		Session::forge();
	}

	/**
	 * auth
	 *
	 * @return bool
	 */
	public static function auth ()
	{
		// check is already logged in?
		if (Session::show('auth', 'uid')) return TRUE;

		// is post exists?
		$username = Input::post('username', false);
		$password = Input::post('password', false);

		if ( ! $username && ! $username ) return false;
		$users = Users::fetch_users();
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
	 * @return void
	 */
	public static function logout ()
	{
		Session::destroy();
	}

	/**
	 * hash
	 *
	 * @param $str
	 * @return string
	 */
	public static function hash ($str)
	{
		return password_hash($str, CRYPT_BLOWFISH);
	}

	/**
	 * verify
	 *
	 * @param $str
	 * @return string
	 */
	public static function verify ($password, $hash)
	{
		return password_verify($password, $hash);
	}
}
