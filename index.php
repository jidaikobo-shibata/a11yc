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

// a11yc
namespace A11yc;
require (__DIR__.'/libs/a11yc/main.php');

// set users before authentication
Users::forge(unserialize(A11YC_USERS));

// auth
Auth::forge();
if (Auth::auth())
{
	// backup and version check, this must not run so frequently.
	if (Maintenance::leave_at_least_a_day())
	{
		// backup
		Maintenance::sqlite(A11YC_DATA_PATH, A11YC_DATA_FILE);

		// version check
		Maintenance::version_check();

		// security check
		Security::deny_http_directories();
	}

	// login user
	$login_user = Users::fetch_current_user();
	View::assign('login_user', $login_user);
}

// route
Route::forge();
$controller = Route::get_controller();
$action = Route::get_action();
$controller::$action();

// assign mode
$mode = strtolower(substr($controller, strpos($controller, '_') + 1));
View::assign('mode', $mode);

// render
View::display(array(
		'header.php',
		'messages.php',
		'body.php',
		'footer.php',
	));
