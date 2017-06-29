<?php
/**
 * A11yc\Center
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Controller_Center
{
	/**
	 * action
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		static::index();
	}

	/**
	 * Show A11y Center Index
	 *
	 * @return  void
	 */
	public static function index()
	{
		$setup = Controller_Setup::fetch_setup();

		$body = '';
		if ( ! empty($setup))
		{
			// report page
			View::assign('is_center', TRUE);
			Controller_Disclosure::report();
			$body = View::fetch('body');
		}

		// add report to center information
		$center = View::fetch_tpl('center/index.php');
		View::assign('title', A11YC_LANG_CENTER_TITLE);
		View::assign('body', $body.$center, false);
	}
}
