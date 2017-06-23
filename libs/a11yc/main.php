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
define('A11YC_VERSION', '0.9.3');
// git tag 0.9.2 & git push origin --tags

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

// load Spyc - YAML lib.
include A11YC_LIB_PATH.'/spyc/Spyc.php';

// Autoloader - this must be use Kontiki namespace.
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');

// language
$lang = Lang::get_lang() ?: A11YC_LANG;
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.$lang);
include A11YC_PATH.'/languages/'.$lang.'/a11yc.php';

// install a11yc if not yet
Startup::install();

// database
Db::forge(array(
		'dbtype' => 'sqlite',
		'path' => A11YC_DATA_PATH.A11YC_DATA_FILE,
	));
Db::init_table();

// startup a11yc
Startup::check_progress();

// view
View::forge(A11YC_PATH.'/views/');
