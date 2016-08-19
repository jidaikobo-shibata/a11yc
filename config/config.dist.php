<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', 'http://example.com/a11yc/index.php');
define('A11YC_URL_DIR', dirname(A11YC_URL));

// language
define('A11YC_LANG', 'ja');

// path
define('A11YC_PATH', dirname(__DIR__));
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');
define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.A11YC_LANG);

// database - sqlite or mysql
define('A11YC_DBTYPE', 'sqlite');

// sqlite
define('A11YC_SQLITE_PATH',   A11YC_PATH.'/db/db.sqlite');

// mysql
define('A11YC_MYSQL_NAME', '');
define('A11YC_MYSQL_USER', '');
define('A11YC_MYSQL_HOST', '');
define('A11YC_MYSQL_PASSWORD', '');

// tables
define('A11YC_TABLE_PAGES',  'a11y_pages');
define('A11YC_TABLE_CHECKS', 'a11y_checks');
define('A11YC_TABLE_SETUP',  'a11y_setup');
define('A11YC_TABLE_BULK',   'a11y_bulk');

// url
define('A11YC_BULK_URL',     A11YC_URL.'?mode=bulk');
define('A11YC_PAGES_URL',     A11YC_URL.'?mode=pages');
define('A11YC_CHECKLIST_URL', A11YC_URL.'?mode=checklist&amp;url=');
define('A11YC_DOC_URL',       A11YC_URL.'?mode=docs_each&amp;code=');

// target
define('A11YC_TARGET',     ' target="a11y_target"');
define('A11YC_TARGET_OUT', ' target="a11y_target_out"');
