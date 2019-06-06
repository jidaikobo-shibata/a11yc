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

		// update MySQL value field
		Update\UpdateMysqlValueField::update();

		$created = Model\Data::fetch('db_create', 'global');

		if ( ! $created) return;
		Model\Data::delete('db_create', 'global');

		// update 1.x.x -> 2.x.x
		Update\One2Two::update();

		// update add seq to pages
		Update\AddSeq2Page::update();

		// update add trash to issues
		Update\AddTrash2Issue::update();

		// update settings table
		Update\UpdateSetting::update();

		// update to KVS
		Update\AddData::update();
	}
}
