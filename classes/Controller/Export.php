<?php
/**
 * A11yc\Controller\Export
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Export
{
	use ExportResult;
	use ExportCsv;
	use ExportIssue;

	/**
	 * action
	 * trait: ExportResult
	 *
	 * @return Void
	 */
	public static function actionResultexport()
	{
		static::export();
	}

	/**
	 * action
	 * trait: ExportResult
	 *
	 * @return Void
	 */
	public static function actionResultimport()
	{
		static::import();
	}

	/**
	 * action
	 * trait: ExportCsv
	 *
	 * @return Void
	 */
	public static function actionCsv()
	{
//		$url = Util::enuniqueUri(Input::param('url', '', FILTER_VALIDATE_URL));
		$url = Util::enuniqueUri(Input::param('url', ''));
		static::csv($url);
	}

	/**
	 * action
	 * trait: ExportIssue
	 *
	 * @return Void
	 */
	public static function actionIssue()
	{
		static::issue();
	}
}
