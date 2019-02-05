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

use A11yc\Model;

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

		$created = Model\Data::fetch('db_create', 'global');

		if ( ! $created) return;
		Model\Data::delete('db_create', 'global');

		// update 1.x.x -> 2.x.x
		if (
			Db::isTableExist(A11YC_TABLE_SETUP_OLD) &&
			! Db::isTableExist(A11YC_TABLE_CACHES)
		)
		{
			Update\One2Two::update();
		}

		// update add seq to pages
		if (
			Db::isTableExist(A11YC_TABLE_PAGES) &&
			! Db::isFieldsExist(A11YC_TABLE_PAGES, array('seq'))
		)
		{
			Update\AddSeq2Page::update();
		}

		// update add trash to issues
		if (
			Db::isTableExist(A11YC_TABLE_ISSUES) &&
			! Db::isFieldsExist(A11YC_TABLE_ISSUES, array('trash'))
		)
		{
			Update\AddTrash2Issue::update();
		}

		// update settings table
		if (
			Db::isTableExist(A11YC_TABLE_SETTINGS) &&
			Db::isFieldsExist(A11YC_TABLE_SETTINGS, array('target_level'))
		)
		{
			Update\UpdateSetting::update();
		}

		Update\AddData::update();
	}
}
