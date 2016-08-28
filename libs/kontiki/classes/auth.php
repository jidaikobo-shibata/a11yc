<?php
/**
 * Kontiki\Auth
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class Auth
{
	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct()
	{
		session_start();
	}

	/**
	 * auth
	 *
	 * @return bool
	 */
	public static function auth ()
	{
		if (isset($_SESSION['uid'])) return TRUE;

		$username = isset($_POST['username']) ? $_POST['username'] : false;
		$password = isset($_POST['password']) ? $_POST['password'] : false;

		if ( ! $username && ! $username ) return false;

		$users = Users::fetch_users();
		foreach ($users as $id => $v)
		{
			if ($v[0] === $username && $v[1] === $password)
			{
				$_SESSION['uid'] = $id;
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
		unset($_SESSION['uid']);
	}

	/**
	 * login form
	 *
	 * @return string
	 */
	public static function login_form ()
	{
		\A11yc\View::assign('title', A11YC_LANG_LOGIN_TITLE);
		\A11yc\View::assign('body', \A11yc\View::fetch_tpl('auth/login.php'), false);
	}
}
