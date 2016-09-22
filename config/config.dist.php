<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', 'http://example.com/a11yc/index.php');

// users
define('A11YC_USERS', serialize(array(
	1 => array('root', 'password', 'root', ''),
)));

// for css and js
define('A11YC_URL_DIR', dirname(A11YC_URL).'/libs/a11yc');

// language
define('A11YC_LANG', 'ja');

// path
define('A11YC_PATH', dirname(__DIR__).'/libs/a11yc');
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.A11YC_LANG);

// tables
define('A11YC_TABLE_PAGES',  'a11y_pages');
define('A11YC_TABLE_CHECKS', 'a11y_checks');
define('A11YC_TABLE_SETUP',  'a11y_setup');
define('A11YC_TABLE_BULK',   'a11y_bulk');

// url
define('A11YC_BULK_URL',      A11YC_URL.'?c=bulk&amp;a=index');
define('A11YC_PAGES_URL',     A11YC_URL.'?c=pages&amp;a=index');
define('A11YC_CHECKLIST_URL', A11YC_URL.'?c=checklist&amp;a=index&amp;url=');
define('A11YC_DOC_URL',       A11YC_URL.'?c=docs&amp;a=each&amp;code=');

// target
define('A11YC_TARGET',     ' target="a11y_target"');
define('A11YC_TARGET_OUT', ' target="a11y_target_out"');
