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
	public static $user_id;

	/**
	 * __construct
	 *
	 * @return  void
	 */
	public function __construct()
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
		unset($_SESSION['uid']);
	}

	/**
	 * login form
	 *
	 * @return string
	 */
	public static function login_form ()
	{
		\Kontiki\View::assign('title', A11YC_LANG_LOGIN_TITLE);
		\Kontiki\View::assign('body', \Kontiki\View::fetch_tpl('auth/login.php'), false);
	}
}
