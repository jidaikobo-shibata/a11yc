<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

//error_reporting
//ini_set('error_reporting', E_ALL | ~E_STRICT);
//error_reporting(0);

//convert_time and memorty
$startTime = microtime(true) ;
$startMemory = memory_get_usage(false) ;

// kontiki
define('KONTIKI_CONFIG_PATH', __DIR__.'/config/kontiki.php');
require (__DIR__.'/libs/kontiki/main.php');

// a11yc
require (__DIR__.'/config/config.php');
require (A11YC_PATH.'/main.php');

// database
\A11yc\Db::forge();
\A11yc\Db::init_table();

// routing
$mode = isset($_GET['mode']) ? \A11yc\Util::s($_GET['mode']) : 'center' ;

// auth
$auth = new \A11yc\Auth();
$is_logged_in = $auth->auth();

// logout
if ($is_logged_in && $mode == 'logout')
{
	$auth->logout();
	header('location:'.A11YC_URL);
	exit();
}

// urls for checklist
$url = isset($_GET['url']) ? $_GET['url'] : '';

// assign
if ( ! $is_logged_in)
{
	\A11yc\Auth::login_form();
	$mode = 'login';
}
else
{
	switch ($mode)
	{
		case 'center':
			\A11yc\Center::index();
			break;
		case 'setup':
			\A11yc\Setup::index();
			break;
		case 'pages':
			\A11yc\Pages::index();
			break;
		case 'checklist':
			\A11yc\Checklist::checklist($url);
			break;
		case 'bulk':
			\A11yc\Bulk::checklist('bulk');
			break;
		case 'docs':
			\A11yc\Docs::index();
			break;
		case 'docs_each':
			$criterion = isset($_GET['criterion']) ? $_GET['criterion'] : '';
			$code = isset($_GET['code']) ? $_GET['code'] : '';
			\A11yc\Docs::each($criterion, $code);
			break;
	}
}

// assign performance
\A11yc\Util::performance($startTime, $startMemory) ;

// render
\A11yc\View::assign('mode', $mode);
\A11yc\View::display();
