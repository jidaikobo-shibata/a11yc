<?php
/**
 * Kontiki\Auth
 *
 * @package    part of Kontiki
 * @version    1.0
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
	 * _init
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
		if (Session::show('auth', 'uid')) return TRUE;

		$username = isset($_POST['username']) ? $_POST['username'] : false;
		$password = isset($_POST['password']) ? $_POST['password'] : false;

		if ( ! $username && ! $username ) return false;

		$users = Users::fetch_users();
		foreach ($users as $id => $v)
		{
			if ($v[0] === $username && $v[1] === $password)
			{
				Session::add('auth', 'uid', $id);
				static::$user_id = $id;
				return TRUE;
			}
		}
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
}
