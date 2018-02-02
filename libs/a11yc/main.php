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
define('A11YC_VERSION', '1.1.2');
// git tag 1.1.2 & git push origin --tags

// config
$config_path = dirname(dirname(__DIR__)).'/config/config.php';
if ( ! file_exists($config_path))
{
	header('Content-Type: text/plain; charset=UTF-8', true, 403);
	die('set config.');
}
require ($config_path);

// load kontiki
define('KONTIKI_DEFAULT_LANG', A11YC_LANG);
define('KONTIKI_DEFAULT_TIMZONE', A11YC_TIMEZONE);
require A11YC_LIB_PATH.'/kontiki/main.php';

// Autoloader - this must be use Kontiki namespace.
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');

// language
$lang = Lang::get_lang() ?: A11YC_LANG;
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.$lang);
include A11YC_PATH.'/languages/'.$lang.'/a11yc.php';

// install a11yc if not yet
Startup::install();

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
Db::init_table();

// startup a11yc
Startup::check_progress();

// view
View::forge(A11YC_PATH.'/views/');
