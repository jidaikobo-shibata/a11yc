<?php
/**
 * A11yc\Update
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

// old tables
define('A11YC_TABLE_SETUP_OLD',       'a11y_setup');
define('A11YC_TABLE_PAGES_OLD',       'a11y_pages');
define('A11YC_TABLE_CHECKS_OLD',      'a11y_checks');
define('A11YC_TABLE_CHECKS_NGS_OLD',  'a11y_checks_ngs');
define('A11YC_TABLE_BULK_OLD',        'a11y_bulk');
define('A11YC_TABLE_BULK_NGS_OLD',    'a11y_bulk_ngs');
define('A11YC_TABLE_MAINTENANCE_OLD', 'a11y_maintenance');

class Update
{
	/**
	 * check
	 *
	 * @return Void
	 */
	public static function check()
	{
		if (A11YC_DB_TYPE == 'none') return;

		// update 1.x.x -> 2.x.x
		if (Db::isTableExist(A11YC_TABLE_SETUP_OLD) && ! Db::isTableExist(A11YC_TABLE_CACHES))
		{
			Update\One2Two::update();
		}

		// update add seq to pages
		if ( ! Db::isFieldsExist(A11YC_TABLE_PAGES, array('seq')))
		{
			Update\AddSeq2Pages::update();
		}

		// update add trash to issues
		if ( ! Db::isFieldsExist(A11YC_TABLE_ISSUES, array('trash')))
		{
			Update\AddTrash2Issues::update();
		}

		// update settings table
		if (Db::isFieldsExist(A11YC_TABLE_SETTINGS, array('target_level')))
		{
			Update\UpdateSettings::update();
		}

		// update add `image_path` to issue
		if ( ! Db::isFieldsExist(A11YC_TABLE_ISSUES, array('image_path')))
		{
			Update\AddImgpth2Issues::update();
		}
	}
}
