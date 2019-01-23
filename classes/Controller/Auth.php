<?php
/**
 * A11yc\Controller\Auth
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Auth
{
	/**
	 * Login
	 *
	 * @return Void
	 */
	public static function actionLogin()
	{
		View::assign('title', A11YC_LANG_AUTH_TITLE);
		View::assign('body', View::fetchTpl('auth/login.php'), false);
	}

	/**
	 * Logout
	 *
	 * @param String $redirect
	 * @return Void
	 */
	public static function actionLogout($redirect = A11YC_URL)
	{
		\Kontiki\Auth::logout();
		Util::redirect($redirect);
	}
}
