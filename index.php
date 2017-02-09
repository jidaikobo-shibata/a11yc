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

// database
\A11yc\Db::forge(array(
		'dbtype' => 'sqlite',
		'path' => KONTIKI_DATA_PATH.KONTIKI_DATA_FILE,
	));

// versions
// \A11yc\Db::forge(
// 	'versions',
// 	array(
// 		'dbtype' => 'sqlite',
// 		'path' => KONTIKI_DATA_PATH.'/versions.sqlite',
// 	));

// init table
\A11yc\Db::init_table();

// backup and version check
if (\Kontiki\Auth::auth())
{
//	\A11yc\Maintenance::version_check();
	\A11yc\Maintenance::sqlite();
}

// users
\A11yc\Users::forge(unserialize(A11YC_USERS));

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// route
\A11yc\Route::forge();
$controller = \A11yc\Route::get_controller();
$action = \A11yc\Route::get_action();
$controller::$action();

// render
$mode = strtolower(substr($controller, strpos($controller, '_') + 1));
\A11yc\View::assign('mode', $mode);
\A11yc\View::display(array(
		'header.php',
		'messages.php',
		'body.php',
		'footer.php',
	));
