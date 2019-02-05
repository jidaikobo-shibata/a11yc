<?php
/**
 * A11yc\Update\AddTrash2Issue
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

use A11yc\Model;

class AddTrash2Issue
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		if ( ! Db::isTableExist(A11YC_TABLE_ISSUES)) return;
		if (Db::isFieldsExist(A11YC_TABLE_ISSUES, array('trash'))) return;

		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `trash` BOOL NOT NULL DEFAULT 0;';
		Db::execute($sql);

		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
		foreach (Db::fetchAll($sql) as $v)
		{
			Model\Issue::update($v['id'], 'trash', 0);
		}
	}
}
