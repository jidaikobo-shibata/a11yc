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

class Update
{
	/**
	 * check
	 *
	 * @return Void
	 */
	public static function check()
	{
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
	}
}
