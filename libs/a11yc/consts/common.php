<?php
/**
 * constants
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// download
define('A11YC_NON_DOWNLOAD_START', '<!-- a11yc non download -->');
define('A11YC_NON_DOWNLOAD_END',   '<!-- /a11yc non download -->');

// target
// define('A11YC_TARGET',     ' target="a11yc_target"');
// define('A11YC_TARGET_OUT', ' target="a11yc_target_out"');
define('A11YC_TARGET',     '');
define('A11YC_TARGET_OUT', '');

// old tables - leave these tables for lower compatibility
if ( ! defined('A11YC_TABLE_SETUP_OLD'))
{
	define('A11YC_TABLE_SETUP_OLD',       'a11y_setup');
	define('A11YC_TABLE_PAGES_OLD',       'a11y_pages');
	define('A11YC_TABLE_CHECKS_OLD',      'a11y_checks');
	define('A11YC_TABLE_CHECKS_NGS_OLD',  'a11y_checks_ngs');
	define('A11YC_TABLE_BULK_OLD',        'a11y_bulk');
	define('A11YC_TABLE_BULK_NGS_OLD',    'a11y_bulk_ngs');
	define('A11YC_TABLE_MAINTENANCE_OLD', 'a11y_maintenance');
}

// tables
if ( ! defined('A11YC_TABLE_PAGES'))
{
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
	define('A11YC_TABLE_ICLS',        'a11yc_icls');
	define('A11YC_TABLE_ICLSSIT',     'a11yc_iclssit');
}

// urls
if ( ! defined('A11YC_SETTING_URL'))
{
	define('A11YC_SETTING_URL',      A11YC_URL.'?c=settings&amp;a=');
	define('A11YC_BULK_URL',         A11YC_URL.'?c=bulk&amp;a=');
	define('A11YC_PAGES_URL',        A11YC_URL.'?c=pages&amp;a=');
	define('A11YC_CHECKLIST_URL',    A11YC_URL.'?c=checklist&amp;a=check&amp;url=');
	define('A11YC_RESULTS_EACH_URL', A11YC_URL.'?c=results&amp;a=each&amp;url=');
	define('A11YC_ISSUES_URL',       A11YC_URL.'?c=issues&amp;a=');
	define('A11YC_IMAGELIST_URL',    A11YC_URL.'?c=images&amp;a=view&amp;url=');
	define('A11YC_DOC_URL',          A11YC_URL.'?c=docs&amp;a=each&amp;criterion=');
	define('A11YC_LIVE_URL',         A11YC_URL.'?c=live&amp;a=view&amp;url=');
	define('A11YC_EXPORT_URL',       A11YC_URL.'?c=export&amp;a=');
	define('A11YC_ICLS_URL',         A11YC_URL.'?c=icls&amp;a=');
}

// for css and js
if ( ! defined('A11YC_ASSETS_URL'))
{
	define('A11YC_ASSETS_URL',    dirname(A11YC_URL).'/assets');
}
