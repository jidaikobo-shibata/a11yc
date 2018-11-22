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

// administration access approved IPs - must be array
define('A11YC_APPROVED_IPS', serialize(array(
	'::1',
	'127.0.0.1',
)));

// post access approved IPs - must be array
define('A11YC_APPROVED_GUEST_IPS', serialize(array(
	'::1',
	'127.0.0.1',
)));

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

/*
 * below here, basically don't edit.
 */

// for css and js
define('A11YC_ASSETS_URL',    dirname(A11YC_URL).'/assets');

// pathes
defined('A11YC_CONFIG_PATH') or define('A11YC_CONFIG_PATH',   dirname(__FILE__));
define('A11YC_LIB_PATH',      dirname(__DIR__).'/libs');
define('A11YC_PATH',          A11YC_LIB_PATH.'/a11yc');
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');

// database
define('A11YC_DB_TYPE', 'sqlite'); // 'mysql', 'sqlite', 'none'
define('A11YC_DATA_PATH', dirname(__DIR__).'/db');
define('A11YC_DATA_FILE', '/db.sqlite');

// download
define('A11YC_NON_DOWNLOAD_START', '<!-- a11yc non download -->');
define('A11YC_NON_DOWNLOAD_END',   '<!-- /a11yc non download -->');

// old tables
define('A11YC_TABLE_SETUP_OLD',       'a11y_setup');
define('A11YC_TABLE_PAGES_OLD',       'a11y_pages');
define('A11YC_TABLE_CHECKS_OLD',      'a11y_checks');
define('A11YC_TABLE_CHECKS_NGS_OLD',  'a11y_checks_ngs');
define('A11YC_TABLE_BULK_OLD',        'a11y_bulk');
define('A11YC_TABLE_BULK_NGS_OLD',    'a11y_bulk_ngs');
define('A11YC_TABLE_MAINTENANCE_OLD', 'a11y_maintenance');

// tables
define('A11YC_TABLE_PAGES',       'a11yc_pages');
define('A11YC_TABLE_UAS',         'a11yc_uas');
define('A11YC_TABLE_CACHES',      'a11yc_caches');
define('A11YC_TABLE_VERSIONS',    'a11yc_versions');
define('A11YC_TABLE_RESULTS',     'a11yc_results');
define('A11YC_TABLE_BRESULTS',    'a11yc_bresults');
define('A11YC_TABLE_CHECKS',      'a11yc_checks');
define('A11YC_TABLE_BCHECKS',     'a11yc_bchecks');
define('A11YC_TABLE_BNGS',        'a11yc_bngs');
define('A11YC_TABLE_ISSUES',      'a11yc_issues');
define('A11YC_TABLE_ISSUESBBS',   'a11yc_issuesbbs');
define('A11YC_TABLE_SETTINGS',    'a11yc_settings');
define('A11YC_TABLE_MAINTENANCE', 'a11yc_maintenance');

// urls
define('A11YC_VALIDATE_URL',        dirname(A11YC_URL));
define('A11YC_SETTING_URL',         A11YC_URL.'?c=settings&amp;a=');
define('A11YC_BULK_URL',            A11YC_URL.'?c=bulk&amp;a=index');
define('A11YC_PAGES_URL',           A11YC_URL.'?c=pages&amp;a=index');
define('A11YC_PAGES_ADD_URL',       A11YC_URL.'?c=pages&amp;a=add');
define('A11YC_PAGES_EDIT_URL',      A11YC_URL.'?c=pages&amp;a=edit');
define('A11YC_CHECKLIST_URL',       A11YC_URL.'?c=checklist&amp;a=check&amp;url=');
define('A11YC_RESULTS_EACH_URL',    A11YC_URL.'?c=results&amp;a=each&amp;url=');
define('A11YC_ISSUES_INDEX_URL',    A11YC_URL.'?c=issues&amp;a=index');
define('A11YC_ISSUES_ADD_URL',      A11YC_URL.'?c=issues&amp;a=add&amp;url='); // and criterion
define('A11YC_ISSUES_EDIT_URL',     A11YC_URL.'?c=issues&amp;a=edit&amp;id=');
define('A11YC_ISSUES_READ_URL',     A11YC_URL.'?c=issues&amp;a=read&amp;id=');
define('A11YC_ISSUES_DELETE_URL',   A11YC_URL.'?c=issues&amp;a=delete&amp;id=');
define('A11YC_ISSUES_UNDELETE_URL', A11YC_URL.'?c=issues&amp;a=undelete&amp;id=');
define('A11YC_ISSUES_PURGE_URL',    A11YC_URL.'?c=issues&amp;a=purge&amp;id=');
define('A11YC_IMAGELIST_URL',       A11YC_URL.'?c=images&amp;a=view&amp;url=');
define('A11YC_DOC_URL',             A11YC_URL.'?c=docs&amp;a=each&amp;criterion=');
define('A11YC_LIVE_URL',            A11YC_URL.'?c=live&amp;a=view&amp;url=');
define('A11YC_EXPORT_URL',          A11YC_URL.'?c=export&amp;a=csv&amp;url=');

// target
// define('A11YC_TARGET',     ' target="a11yc_target"');
// define('A11YC_TARGET_OUT', ' target="a11yc_target_out"');
define('A11YC_TARGET',     '');
define('A11YC_TARGET_OUT', '');
