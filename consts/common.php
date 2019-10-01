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

if ( ! defined('A11YC_UPLOAD_PATH'))
{
	define('A11YC_UPLOAD_PATH', dirname(dirname(__DIR__)).'/screenshots');
	define('A11YC_UPLOAD_URL',  dirname(A11YC_URL).'/screenshots');
}

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

	define('A11YC_TABLE_RESULTS',     'a11yc_results');
	define('A11YC_TABLE_BRESULTS',    'a11yc_bresults');
	define('A11YC_TABLE_BCHECKS',     'a11yc_bchecks');
	define('A11YC_TABLE_MAINTENANCE', 'a11yc_maintenance');
	define('A11YC_TABLE_VERSIONS',    'a11yc_versions');
	define('A11YC_TABLE_UAS',         'a11yc_uas');
	define('A11YC_TABLE_SETTINGS',    'a11yc_settings');
	define('A11YC_TABLE_PAGES',       'a11yc_pages');
	define('A11YC_TABLE_CACHES',      'a11yc_caches');
	define('A11YC_TABLE_CHECKS',      'a11yc_checks');
	define('A11YC_TABLE_ISSUES',      'a11yc_issues');
	define('A11YC_TABLE_ISSUESBBS',   'a11yc_issuesbbs');
}

// tables
if ( ! defined('A11YC_TABLE_DATA'))
{
	define('A11YC_TABLE_DATA', 'a11yc_data');
}

// urls
if ( ! defined('A11YC_SETTING_URL'))
{
	define('A11YC_SETTING_URL',     A11YC_URL.'?c=setting&amp;a=');
	define('A11YC_BULK_URL',        A11YC_URL.'?c=bulk&amp;a=');
	define('A11YC_PAGE_URL',        A11YC_URL.'?c=page&amp;a=');
	define('A11YC_ISSUE_URL',       A11YC_URL.'?c=issue&amp;a=');
	define('A11YC_DATA_URL',        A11YC_URL.'?c=data&amp;a=');
	define('A11YC_DOWNLOAD_URL',    A11YC_URL.'?c=download&amp;a=');
	define('A11YC_ICL_URL',         A11YC_URL.'?c=icl&amp;a=');
	define('A11YC_CHECKLIST_URL',   A11YC_URL.'?c=checklist&amp;a=check&amp;url=');
	define('A11YC_SITECHECK_URL',   A11YC_URL.'?c=sitecheck&amp;a=');
	define('A11YC_RESULT_EACH_URL', A11YC_URL.'?c=result&amp;a=each&amp;url=');
	define('A11YC_DOC_URL',         A11YC_URL.'?c=doc&amp;a=each&amp;criterion=');
	define('A11YC_LIVE_URL',        A11YC_URL.'?c=live&amp;a=view&amp;url=');
}
if ( ! defined('A11YC_IMAGELIST_URL'))
{
	// do not add A11YC_URL
	define('A11YC_IMAGELIST_URL',   '?c=image&amp;a=view&amp;url=');
}

// for css and js
if ( ! defined('A11YC_ASSETS_URL'))
{
	define('A11YC_ASSETS_URL',    dirname(A11YC_URL).'/assets');
}
