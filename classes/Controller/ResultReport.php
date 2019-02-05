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
	 * @return Void
	 */
	public static function report($base_url)
	{
		if (empty($base_url)) Util::error('invalid url was given');
		// settings
		$settings = Model\Setting::fetchAll();
		if ($settings['show_results'] === false) Util::redirect($base_url);

		// common assign
		View::assign('settings', $settings, true);
		View::assign('is_center', FALSE);
		View::assign('base_url', $base_url);

		// assign links
		static::assignLinks();

		// report
		if (Input::get('a11yc_report'))
		{
			// use ResultAll
			static::all();
			return;
		}

		// page list
		if (Input::get('a11yc_page'))
		{
			static::page();
			return;
		}

		// each report
		if (Input::get('url'))
		{
			static::each(Input::get('url', ''));
			return;
		}

		// show policy
		View::assign('versions', Model\Version::fetchAll());
		View::assign('title', A11YC_LANG_POLICY);
		View::assign('body', View::fetchTpl('result/policy.php'), false);
	}
}
