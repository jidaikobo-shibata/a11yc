<?php
/**
 * A11yc\Controller\Output
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Output
{
	use OutputCsv;
	use OutputIssue;

	/**
	 * action
	 *
	 * @return Void
	 */
	public static function actionCsv()
	{
		// trait: OutputCsv
		$url = Util::enuniqueUri(Input::param('url', ''));
		static::csv($url);
	}

	/**
	 * action
	 *
	 * @return Void
	 */
	public static function actionIssue()
	{
		// trait: OutputIssue
		static::issue();
	}
}
