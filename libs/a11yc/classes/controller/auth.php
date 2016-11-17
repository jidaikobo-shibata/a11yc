<?php
/**
 * A11yc\Auth
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Controller_Auth
{
	/**
	 * Login
	 *
	 * @return  void
	 */
	public static function Action_Login()
	{
		\A11yc\View::assign('title', A11YC_LANG_AUTH_TITLE);
		\A11yc\View::assign('body', \A11yc\View::fetch_tpl('auth/login.php'), false);
	}

	/**
	 * Logout
	 *
	 * @return  void
	 */
	public static function Action_Logout()
	{
		\Kontiki\Auth::logout();
		header('location:'.A11YC_URL);
		exit();
	}
}