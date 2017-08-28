<?php
/**
 * A11yc\Auth
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Controller_Auth
{
	/**
	 * Login
	 *
	 * @return Void
	 */
	public static function Action_Login()
	{
		\A11yc\View::assign('title', A11YC_LANG_AUTH_TITLE);
		\A11yc\View::assign('body', \A11yc\View::fetch_tpl('auth/login.php'), false);
	}

	/**
	 * Logout
	 *
	 * @param String $redirect
	 * @return Void
	 */
	public static function Action_Logout($redirect = A11YC_URL)
	{
		\Kontiki\Auth::logout();
		header('location:'.$redirect);
		exit();
	}
}