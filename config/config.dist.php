<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', 'http://example.com/a11yc/index.php');

// language
// en, ja
define('A11YC_LANG', 'ja');

// administration access approved IPs
define('A11YC_APPROVED_IPS', serialize(array(
	'::1',
	'127.0.0.1',
)));

// users
// array must be started with 1 (not 0)
// array(1 => array(username, password, display_name, memo))
// username 'root' is development user to show development information.
define('A11YC_USERS', serialize(array(
	1 => array('admin', 'password', 'administrator', ''),
)));

/*
	below here, basically don't edit.
*/

// for css and js
define('A11YC_URL_DIR', dirname(A11YC_URL).'/libs/a11yc');

// pathes
define('A11YC_PATH',          dirname(__DIR__).'/libs/a11yc');
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.A11YC_LANG);
define('A11YC_CACHE_PATH', dirname(__DIR__).'/cache');

// database
define('KONTIKI_DATA_PATH', dirname(__DIR__).'/db');
define('KONTIKI_DATA_FILE', '/db.sqlite');

// tables
define('A11YC_TABLE_SETUP',      'a11y_setup');
define('A11YC_TABLE_PAGES',      'a11y_pages');
define('A11YC_TABLE_CHECKS',     'a11y_checks');
define('A11YC_TABLE_CHECKS_NGS', 'a11y_checks_ngs');
define('A11YC_TABLE_BULK',       'a11y_bulk');
define('A11YC_TABLE_BULK_NGS',   'a11y_bulk_ngs');

// urls
define('A11YC_VALIDATE_URL',  dirname(A11YC_URL));
define('A11YC_BULK_URL',      A11YC_URL.'?c=bulk&amp;a=index');
define('A11YC_PAGES_URL',     A11YC_URL.'?c=pages&amp;a=index');
define('A11YC_CHECKLIST_URL', A11YC_URL.'?c=checklist&amp;a=index&amp;url=');
define('A11YC_DOC_URL',       A11YC_URL.'?c=docs&amp;a=each&amp;code=');

// target
// define('A11YC_TARGET',     ' target="a11y_target"');
// define('A11YC_TARGET_OUT', ' target="a11y_target_out"');
define('A11YC_TARGET',     '');
define('A11YC_TARGET_OUT', '');
