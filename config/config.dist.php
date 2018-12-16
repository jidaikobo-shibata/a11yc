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

// time zone
define('A11YC_TIMEZONE', 'Asia/Tokyo');

// users
// array must be started with 1 (not 0)
// array(1 => array(username, password, display_name, memo))
// username 'root' is development user to show development information.
// to hash password:
// php -r "echo password_hash('password', CRYPT_BLOWFISH);\n"
define('A11YC_USERS', serialize(array(
	1 => array(
		'admin',
		'$2y$10$4cik/nEzePSnrSwaUsUaleosAHLUPDk0SchWDmn5LCM1LqqqYH6xC', // 'adminpass'
		'administrator',
		''
	),
)));

// administration access approved IPs - must be array
// define('A11YC_APPROVED_IPS', serialize(array(
// 	'::1',
// 	'127.0.0.1',
// )));

// post access approved IPs - must be array
// define('A11YC_APPROVED_GUEST_IPS', serialize(array(
// 	'::1',
// 	'127.0.0.1',
// )));

/*
 * below here, basically don't edit.
 */

// database
define('A11YC_DB_TYPE', 'sqlite'); // 'mysql', 'sqlite', 'none'
define('A11YC_DATA_PATH', dirname(__DIR__).'/db');
define('A11YC_DATA_FILE', '/db.sqlite');

// for css and js
define('A11YC_ASSETS_URL',    dirname(A11YC_URL).'/assets');

// pathes
defined('A11YC_CONFIG_PATH') or define('A11YC_CONFIG_PATH',   dirname(__FILE__));
define('A11YC_LIB_PATH',      dirname(__DIR__).'/libs');
define('A11YC_UPLOAD_PATH',   dirname(__DIR__).'/screenshots');
define('A11YC_PATH',          A11YC_LIB_PATH.'/a11yc');
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');

// urls
define('A11YC_VALIDATE_URL',        dirname(A11YC_URL));
define('A11YC_SETTING_URL',         A11YC_URL.'?c=settings&amp;a=');
define('A11YC_BULK_URL',            A11YC_URL.'?c=bulk&amp;a=index');
define('A11YC_PAGES_URL',           A11YC_URL.'?c=pages&amp;a=index');
define('A11YC_PAGES_ADD_URL',       A11YC_URL.'?c=pages&amp;a=add');
define('A11YC_PAGES_EDIT_URL',      A11YC_URL.'?c=pages&amp;a=edit');
define('A11YC_CHECKLIST_URL',       A11YC_URL.'?c=checklist&amp;a=check&amp;url=');
define('A11YC_RESULTS_EACH_URL',    A11YC_URL.'?c=results&amp;a=each&amp;url=');
define('A11YC_ISSUES_BASE_URL',     A11YC_URL.'?c=issues&amp;a=');
define('A11YC_ISSUES_ADD_URL',      A11YC_URL.'?c=issues&amp;a=add&amp;url='); // and criterion
define('A11YC_ISSUES_EDIT_URL',     A11YC_URL.'?c=issues&amp;a=edit&amp;id=');
define('A11YC_ISSUES_READ_URL',     A11YC_URL.'?c=issues&amp;a=read&amp;id=');
define('A11YC_ISSUES_DELETE_URL',   A11YC_URL.'?c=issues&amp;a=delete&amp;id=');
define('A11YC_ISSUES_UNDELETE_URL', A11YC_URL.'?c=issues&amp;a=undelete&amp;id=');
define('A11YC_ISSUES_PURGE_URL',    A11YC_URL.'?c=issues&amp;a=purge&amp;id=');
define('A11YC_IMAGELIST_URL',       A11YC_URL.'?c=images&amp;a=view&amp;url=');
define('A11YC_DOC_URL',             A11YC_URL.'?c=docs&amp;a=each&amp;criterion=');
define('A11YC_LIVE_URL',            A11YC_URL.'?c=live&amp;a=view&amp;url=');
define('A11YC_EXPORT_URL',          A11YC_URL.'?c=export&amp;a='); // csv&amp;url= || issue&amp;url=
define('A11YC_IMPLEMENT_URL',       A11YC_URL.'?c=implement&amp;a=');
