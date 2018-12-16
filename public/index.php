<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// a11yc
namespace A11yc;
require (dirname(__DIR__).'/libs/a11yc/main.php');

// is ip allowed
if (defined('A11YC_APPROVED_IPS'))
{
	$allowed = unserialize(A11YC_APPROVED_IPS);
	if ( ! in_array($_SERVER['REMOTE_ADDR'], $allowed)) Util::error();
}

// set users before authentication
Users::forge(unserialize(A11YC_USERS));

// auth
Auth::forge();
if (Auth::auth())
{
	// backup and version check, this must not run so frequently.
	if (Maintenance::isFisrtOfToday())
	{
		// backup
		Maintenance::sqlite(A11YC_DATA_PATH, A11YC_DATA_FILE);

		// security check
		Security::denyHttpDirectories();
	}

	// login user
	$login_user = Users::fetchCurrentUser();
	View::assign('login_user', $login_user);
}

// route
Route::forge();
$controller = Route::getController();
$action = Route::getAction();
$controller::$action();

// assign mode
$controllers = explode('\\', $controller);
$mode = strtolower(end($controllers));
View::assign('mode', $mode);

// render
if ($mode == 'live')
{
	View::display(array(
			'body.php',
		));
}
else
{
	View::display(array(
			'header.php',
			'messages.php',
			'body.php',
			'footer.php',
		));
}
