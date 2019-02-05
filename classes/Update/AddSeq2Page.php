<?php
/**
 * A11yc\Update\AddSeq2Page
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class AddSeq2Page
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		if ( ! Db::isTableExist(A11YC_TABLE_PAGES)) return;
		if (Db::isFieldsExist(A11YC_TABLE_PAGES, array('seq'))) return;

		$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `seq` INTEGER DEFAULT 0;';
		Db::execute($sql);
	}
}
