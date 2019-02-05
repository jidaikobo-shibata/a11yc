<?php
/**
 * A11yc\Controller\Data
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Data
{
	use DataExport;
	use DataImport;

	/**
	 * export partial or all data
	 *
	 * @return Void
	 */
	public static function actionExport()
	{
		// trait: DataExport
		static::export();
	}

	/**
	 * import partial or all data
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		// trait: DataImport
		static::import();
	}
}
