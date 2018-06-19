<?php
/**
 * config
 *
 * @package    part of A11yc
 */

// base url
define('A11YC_URL', admin_url('admin.php'));

// a11yc language
include_once WP_PLUGIN_DIR.'/jwp-a11y/classes/Locale.php';
define('A11YC_LANG', \JwpA11y\Locale::get_simple_locale(get_locale()));

// time zone
define('A11YC_TIMEZONE', date_default_timezone_get());

// target
define('A11YC_TARGET',     '');
define('A11YC_TARGET_OUT', '');

// for css and js
define('A11YC_ASSETS_URL', plugins_url('jwp-a11y').'/assets');

// pathes
define('A11YC_LIB_PATH',      WP_PLUGIN_DIR.'/jwp-a11y/libs');
define('A11YC_PATH',          A11YC_LIB_PATH.'/a11yc');
define('A11YC_CONFIG_PATH',   dirname(__FILE__));
define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');

// out of date. but leave it for lower compatibility
define('A11YC_CACHE_PATH', dirname(WP_PLUGIN_DIR).'/jwp-a11y_cache');

// mysql for WordPress
define('A11YC_DB_TYPE', 'mysql');
define('A11YC_DB_NAME', DB_NAME);
define('A11YC_DB_USER', DB_USER);
define('A11YC_DB_HOST', DB_HOST);
define('A11YC_DB_PASSWORD', DB_PASSWORD);

// sqlite for lower compatibility
define('A11YC_DATA_PATH', dirname(WP_PLUGIN_DIR).'/jwp-a11y_db');
define('A11YC_DATA_FILE', '/db.sqlite');

// old tables
global $wpdb;
define('A11YC_TABLE_SETUP_OLD',       $wpdb->prefix.'jwp_a11y_setup');
define('A11YC_TABLE_PAGES_OLD',       $wpdb->prefix.'jwp_a11y_pages');
define('A11YC_TABLE_CHECKS_OLD',      $wpdb->prefix.'jwp_a11y_checks');
define('A11YC_TABLE_CHECKS_NGS_OLD',  $wpdb->prefix.'jwp_a11y_checks_ngs');
define('A11YC_TABLE_BULK_OLD',        $wpdb->prefix.'jwp_a11y_bulk');
define('A11YC_TABLE_BULK_NGS_OLD',    $wpdb->prefix.'jwp_a11y_bulk_ngs');
define('A11YC_TABLE_MAINTENANCE_OLD', $wpdb->prefix.'jwp_a11y_maintenance');

// tables
define('A11YC_TABLE_PAGES',       $wpdb->prefix.'jwp_a11yc_pages');
define('A11YC_TABLE_UAS',         $wpdb->prefix.'jwp_a11yc_uas');
define('A11YC_TABLE_CACHES',      $wpdb->prefix.'jwp_a11yc_caches');
define('A11YC_TABLE_VERSIONS',    $wpdb->prefix.'jwp_a11yc_versions');
define('A11YC_TABLE_RESULTS',     $wpdb->prefix.'jwp_a11yc_results');
define('A11YC_TABLE_BRESULTS',    $wpdb->prefix.'jwp_a11yc_bresults');
define('A11YC_TABLE_CHECKS',      $wpdb->prefix.'jwp_a11yc_checks');
define('A11YC_TABLE_BCHECKS',     $wpdb->prefix.'jwp_a11yc_bchecks');
define('A11YC_TABLE_BNGS',        $wpdb->prefix.'jwp_a11yc_bngs');
define('A11YC_TABLE_ISSUES',      $wpdb->prefix.'jwp_a11yc_issues');
define('A11YC_TABLE_ISSUESBBS',   $wpdb->prefix.'jwp_a11yc_issuesbbs');
define('A11YC_TABLE_SETTINGS',    $wpdb->prefix.'jwp_a11yc_settings');
define('A11YC_TABLE_MAINTENANCE', $wpdb->prefix.'jwp_a11yc_maintenance');

// urls
$urlbase = A11YC_URL.'?page=jwp-a11y%2F';
define('A11YC_VALIDATE_URL',     A11YC_URL);
define('A11YC_SETTING_URL',      $urlbase.'jwp_a11y_settings&amp;a=');
define('A11YC_BULK_URL',         $urlbase.'jwp_a11y_bulk&amp;a=index');
define('A11YC_PAGES_URL',        $urlbase.'jwp_a11y_pages&amp;a=index');
define('A11YC_PAGES_ADD_URL',    $urlbase.'jwp_a11y_pages&amp;a=add');
define('A11YC_PAGES_EDIT_URL',   $urlbase.'jwp_a11y_pages&amp;a=edit');
define('A11YC_CHECKLIST_URL',    $urlbase.'jwp_a11y_checklist&amp;a=check&amp;url=');
define('A11YC_RESULTS_EACH_URL', $urlbase.'jwp_a11y_checklist&amp;a=each&amp;url=');
define('A11YC_ISSUES_ADD_URL',   $urlbase.'jwp_a11y_issues&amp;a=add&amp;url='); // and criterion
define('A11YC_ISSUES_EDIT_URL',  $urlbase.'jwp_a11y_issues&amp;a=edit&amp;id=');
define('A11YC_ISSUES_VIEW_URL',  $urlbase.'jwp_a11y_issues&amp;a=view&amp;id=');
define('A11YC_IMAGELIST_URL',    $urlbase.'jwp_a11y_checklist&amp;a=images&amp;url=');
define('A11YC_DOC_URL',          $urlbase.'jwp_a11y_docs&amp;a=each&amp;code=');
define('A11YC_LIVE_URL',         $urlbase.'jwp_a11y_checklist&amp;a=view&amp;url=');
define('A11YC_EXPORT_URL',       $urlbase.'jwp_a11y_checklist&amp;a=csv&amp;url=');
