<?php
/**
 * A11yc\Center
 *
 * @package    part of A11yc
 * @version    1.0
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
		// report page
		View::assign('is_center', TRUE);
		Controller_Disclosure::report();

		// add report to center information
		$body = View::fetch('body');
		$center = View::fetch_tpl('center/index.php');
		View::assign('body', $body.$center, false);
	}
}
