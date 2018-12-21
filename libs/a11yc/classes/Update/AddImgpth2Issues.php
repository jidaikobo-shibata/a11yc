<?php
/**
 * A11yc\Update\AddImgpth2Issues
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class AddImgpth2Issues
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `image_path` TEXT DEFAULT "";';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `title` TEXT DEFAULT "";';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_ISSUES.' ADD `seq` INTEGER DEFAULT 0;';
		Db::execute($sql);

		$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `image_path` TEXT DEFAULT "";';
		Db::execute($sql);
	}
}
