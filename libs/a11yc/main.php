<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// config
$config_path = dirname(dirname(__DIR__)).'/config/config.php';
if ( ! file_exists($config_path))
{
	header('Content-Type: text/plain; charset=UTF-8', true, 500);
	die('check config/config.php');
}
require ($config_path);

// load kontiki
define('KONTIKI_DEFAULT_LANG', A11YC_LANG);
require (dirname(dirname(__DIR__)).'/libs/kontiki/main.php');

// language
include A11YC_PATH.'/languages/'.A11YC_LANG.'.php';

// Spyc - YAML lib.
include dirname(dirname(__DIR__)).'/libs/spyc/Spyc.php';

// Autoloader
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');

// security check
$htaccess_path = dirname(__DIR__).'/.htaccess';
if ( ! file_exists($htaccess_path))
{
	\Kontiki\Util::error('Security Risk found. check '.$htaccess_path);
}
