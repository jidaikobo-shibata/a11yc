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
		// title
		$title = A11YC_LANG_LOGIN_TITLE;

		// html
		$html = '';
		$html.= '<h1>'.$title.'</h1>';

		// error
		if (isset($_POST['username']))
		{
			$html.= '<p><strong>'.A11YC_LANG_LOGIN_ERROR0.'</strong></p>';
		}

		// form
		$html.= '<form action="" method="POST">';
		$html.= '<label for="a11yc_username">'.A11YC_LANG_LOGIN_USERNAME.'</label>';
		$html.= '<input type="text" name="username" id="a11yc_username" size="20" value="" />';

		$html.= '<label for="a11yc_password">'.A11YC_LANG_LOGIN_PASWWORD.'</label>';
		$html.= '<input type="password" name="password" id="a11yc_password" size="20" value="" />';
		$html.= '<input type="submit" value="'.A11YC_LANG_LOGIN_BTN.'" />';
		$html.= '</form>';
		return array($title, $html);
	}
}
