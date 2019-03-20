<?php
/**
 * A11yc\Controller\Download
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Download
{
	use DownloadCsv;
	use DownloadIssue;
	use DownloadReport;
	use DownloadHtml;

	/**
	 * csv
	 *
	 * @return Void
	 */
	public static function actionCsv()
	{
		// trait: DownloadCsv
		static::csv();
	}

	/**
	 * issue
	 *
	 * @return Void
	 */
	public static function actionIssue()
	{
		// trait: DownloadIssue
		static::issue();
	}

	/**
	 * report
	 *
	 * @return Void
	 */
	public static function actionReport()
	{
		// trait: DownloadReport
		static::report();
	}

	/**
	 * Html
	 *
	 * @return Void
	 */
	public static function actionHtml()
	{
		// trait: DownloadHtml
		static::html();
	}
}
