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

//error_reporting
//ini_set('error_reporting', E_ALL | ~E_STRICT);
//error_reporting(0);
//date_default_timezone_set('Asia/Tokyo');

// a11yc
require (__DIR__.'/libs/a11yc/main.php');

// set users before authentication
\A11yc\Users::forge(unserialize(A11YC_USERS));

// auth
if (\A11yc\Auth::auth())
{
	// backup and version check, this must not run so frequently.
	if (\A11yc\Maintenance::leave_at_least_a_day())
	{
		// backup
		\A11yc\Maintenance::sqlite(A11YC_DATA_PATH, A11YC_DATA_FILE);

		// version check
		\A11yc\Maintenance::version_check();

		// security check
		\A11yc\Security::deny_http_directories();
	}

	// login user
	$login_user = \A11yc\Users::fetch_current_user();
	\A11yc\View::assign('login_user', $login_user);
}

// route
\A11yc\Route::forge();
$controller = \A11yc\Route::get_controller();
$action = \A11yc\Route::get_action();
$controller::$action();

// assign mode
$mode = strtolower(substr($controller, strpos($controller, '_') + 1));
\A11yc\View::assign('mode', $mode);

// render
\A11yc\View::display(array(
		'header.php',
		'messages.php',
		'body.php',
		'footer.php',
	));
