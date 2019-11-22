<?php
/**
 * A11yc\Controller\ResultReport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait ResultReport
{
	/**
	 * show results for public
	 * show variable report by query strings
	 *
	 * @param String $base_url
	 * @param Bool $is_index
	 * @return Void
	 */
	public static function report($base_url, $is_index = false)
	{
		$settings = Model\Setting::fetchAll();
		View::assign('title', A11YC_LANG_POLICY);

		if (empty($base_url) || $settings['show_results'] === false)
		{
			View::assign('body', $settings["policy"], false);
			return;
		}

		// common assign
		View::assign('settings', $settings, false);

		// report
		if (Input::get('a11yc_report'))
		{
			// use ResultAll
			static::all(false, $base_url);
			return;
		}

		// page list
		if (Input::get('a11yc_page'))
		{
			// use ResultPage
			static::page($base_url);
			return;
		}

		// each report
		if (Input::get('a11yc_each') && Input::get('url'))
		{
			// use ResultEach
			static::each(Input::get('url', ''), $base_url);
			return;
		}

		// show policy
		static::assignLinks($base_url);
		View::assign('versions', Model\Version::fetchAll());
		View::assign('title', A11YC_LANG_POLICY);
		View::assign('body', View::fetchTpl('result/policy.php'), false);
	}
}
