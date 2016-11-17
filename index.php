<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

//error_reporting
//ini_set('error_reporting', E_ALL | ~E_STRICT);
//error_reporting(0);
//date_default_timezone_set('Asia/Tokyo');

// kontiki and a11yc
if ( ! file_exists(__DIR__.'/config/config.php')) die('check config/config.php');
require (__DIR__.'/config/config.php');
require (__DIR__.'/libs/kontiki/main.php');
require (A11YC_PATH.'/main.php');

// database
\A11yc\Db::forge(array(
	'dbtype' => 'sqlite',
	'path' => __DIR__.'/db/db.sqlite',
));
\A11yc\Db::init_table();

// users
\A11yc\Users::forge(unserialize(A11YC_USERS));

// view
\A11yc\View::forge(A11YC_PATH.'/views/');

// route
\A11yc\Route::forge();
$controller = \A11yc\Route::get_controller();
$action = \A11yc\Route::get_action();

// auth
if( ! \Kontiki\Auth::auth())
{
	$controller = '\A11yc\Controller_Auth';
	$action = 'Action_Login';
}

// controller
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
