<?php
/**
 * A11yc\Update\AddSeq2Pages
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

use A11yc\Model;

class AddSeq2Pages extends A11yc\Update
{
	/**
	 * udpate
	 *
	 * @return Void
	 */
	protected static function udpate()
	{
		$sql = 'ALTER TABLE '.A11YC_TABLE_PAGES.' ADD `seq` INTEGER DEFAULT 0;';
		Db::execute($sql);
	}
}
