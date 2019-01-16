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
			return;
		}
		elseif (Input::get('url'))
		{
			static::each();
			return;
		}

		static::index();
	}
	/**
	 * action
	 *
	 * @return Void
	 */
	public static function actionDownload()
	{
		if ( ! \A11yc\Auth::auth()) Util::error();
		$is_full = true;
		Report::download($is_full);
	}

	/**
	 * Show A11y Center Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		$body = '';
		$settings = Model\Setting::fetchAll();
		if ( ! empty($settings))
		{
			$is_center = true;
			Result::all($is_center);
			$result = View::fetch('body');
		}

		View::assign('result', $result, false);
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
		Result::pages();
	}

	/**
	 * Show each page
	 *
	 * @return Void
	 */
	public static function each()
	{
		Result::each(Input::get('url', ''));
	}
}
