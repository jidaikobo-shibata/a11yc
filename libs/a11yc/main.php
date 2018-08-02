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
namespace A11yc;

// version
define('A11YC_VERSION', '2.0.5');
// git tag 2.0.4 & git push origin --tags

// config
$config_path = dirname(dirname(__DIR__)).'/config/config.php';
if ( ! file_exists($config_path))
{
	header('Content-Type: text/plain; charset=UTF-8', true, 403);
	die('set config.');
}
include $config_path;

// load kontiki
define('KONTIKI_DEFAULT_LANG',    A11YC_LANG);
define('KONTIKI_DEFAULT_TIMZONE', A11YC_TIMEZONE);
define('KONTIKI_DB_TYPE',         A11YC_DB_TYPE);
include A11YC_LIB_PATH.'/kontiki/main.php';

// Autoloader - this must be use Kontiki namespace.
\Kontiki\Autoloader::addCoreNamespace(KONTIKI_PATH.'/classes/', 'Kontiki');
\Kontiki\Autoloader::addCoreNamespace(A11YC_PATH.'/classes/', 'A11yc');
\Kontiki\Autoloader::addPath(A11YC_PATH.'/classes/', 'A11yc');

// language
$lang = Lang::getLang() ?: A11YC_LANG;
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.$lang);
include A11YC_PATH.'/languages/'.$lang.'/a11yc.php';

// database
if (defined('A11YC_DB_TYPE') && A11YC_DB_TYPE == 'mysql')
{
	// forge MySQL
	Db::forge(
		array(
			'dbtype'   => 'mysql',
			'db'       => A11YC_DB_NAME,
			'user'     => A11YC_DB_USER,
			'host'     => A11YC_DB_HOST,
			'password' => A11YC_DB_PASSWORD,
		));
}
else
{
	Db::forge(array(
			'dbtype' => 'sqlite',
			'path' => A11YC_DATA_PATH.A11YC_DATA_FILE,
		));
}

// init
Db::initTable();

// update
Update::check();

// setup
if ( ! defined('A11YC_IS_GUEST_VALIDATION'))
{
	if ( ! Input::post('target_level') && ! Arr::get(Model\Settings::fetchAll(), 'target_level'))
	{
		Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
	}
	if ( ! Input::post('base_url') && ! Arr::get(Model\Settings::fetchAll(), 'base_url'))
	{
		Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_BASE_URL);
	}
}

// view
View::forge(A11YC_PATH.'/views/');
