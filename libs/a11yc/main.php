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

// version
define('A11YC_VERSION', '0.9.2');
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
require dirname(dirname(__DIR__)).'/libs/kontiki/main.php';

// load Spyc - YAML lib.
include dirname(dirname(__DIR__)).'/libs/spyc/Spyc.php';

// Autoloader - this must be use Kontiki namespace.
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');

// language
$lang = \A11yc\Lang::get_lang() ?: A11YC_LANG;
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.$lang);
include A11YC_PATH.'/languages/'.$lang.'/a11yc.php';

// install a11yc if not yet
\A11yc\Startup::install();

// database
\A11yc\Db::forge(array(
		'dbtype' => 'sqlite',
		'path' => A11YC_DATA_PATH.A11YC_DATA_FILE,
	));
\A11yc\Db::init_table();

// startup a11yc
\A11yc\Startup::check_progress();

// view
\A11yc\View::forge(A11YC_PATH.'/views/');
