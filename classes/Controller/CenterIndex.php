<?php
/**
 * A11yc\Controller\CenterIndex
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait CenterIndex
{
	/**
	 * Show A11y Center Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		$base_url = Util::uri();

		// page list
		if (Input::get('a11yc_page'))
		{
			// use ResultPage
			Result::page($base_url, true);
		}

		// each report
		elseif (Input::get('a11yc_each') && Input::get('url'))
		{
			// use ResultEach
			Result::each(Input::get('url', ''), $base_url, false, true);
		}

		// all
		elseif ( ! empty(Model\Setting::fetchAll()))
		{
			Result::all($base_url, true);
		}

		View::assign('result', View::fetch('body'), false);
		View::assign('title', A11YC_LANG_CENTER_TITLE);
		View::assign('body', View::fetchTpl('center/index.php'), false);
	}
}
