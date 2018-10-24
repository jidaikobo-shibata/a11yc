<?php
/**
 * A11yc\Controller\Center
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Center
{
	/**
	 * action
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		if (Input::get('a11yc_pages', false))
		{
			static::pages();
		}
		elseif (Input::get('url'))
		{
			static::each();
		}

		static::index();
	}

	/**
	 * Show A11y Center Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		$body = '';
		$settings = Model\Settings::fetchAll();
		if ( ! empty($settings))
		{
			$is_center = true;
			Results::all($is_center);
			$body = View::fetch('body');
		}

		View::assign('title', A11YC_LANG_CENTER_TITLE);
		$center = View::fetchTpl('center/index.php');
		View::assign('body', $body.$center, false);
	}

	/**
	 * Show pages
	 *
	 * @return Void
	 */
	public static function pages()
	{
		Results::pages();
	}

	/**
	 * Show each page
	 *
	 * @return Void
	 */
	public static function each()
	{
		$url = Util::urldec(Input::get('url', '', FILTER_VALIDATE_URL));
		Results::each($url);
	}
}
