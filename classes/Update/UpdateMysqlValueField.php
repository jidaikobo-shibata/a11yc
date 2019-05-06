<?php
/**
 * A11yc\Update\UpdateMysqlValueField
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class UpdateMysqlValueField
{
	/**
	 * update
	 *
	 * @return Void
	 */
	public static function update()
	{
		if (A11YC_DB_TYPE != 'mysql') return;
		$sql = 'SHOW COLUMNS FROM '.A11YC_TABLE_DATA.';';
		foreach (Db::fetchAll($sql) as $v)
		{
			if ($v['Field'] != 'value' || $v['Type'] != 'text') continue;
			$sql = 'ALTER TABLE '.A11YC_TABLE_DATA.' CHANGE `value` ';
			$sql.= '`value` LONGTEXT CHARACTER SET utf8;';
			Db::execute($sql);
		}
	}
}
