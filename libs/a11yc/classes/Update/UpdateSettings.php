<?php
/**
 * A11yc\Update\UpdateSettings
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Update;

class UpdateSettings extends A11yc\Update
{
	/**
	 * update
	 *
	 * @return Void
	 */
	protected static function update()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS;
		$data = Db::fetchAll($sql);

		self::dropSettings();
		Db::createSettings('default');

		foreach ($data as $each)
		{
			$version = intval($each['version']);
			foreach ($each as $k => $v)
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS;
				$sql.= ' (`key`, `value`, `version`) VALUES (?, ?, ?);';
				Db::execute($sql, array($k, $v, $version));
			}
		}
	}

	/**
	 * dropSettings
	 *
	 * @return Void
	 */
	protected static function dropSettings()
	{
		$sql = 'DROP TABLE '.A11YC_TABLE_SETTINGS;
		Db::execute($sql);
	}
}
