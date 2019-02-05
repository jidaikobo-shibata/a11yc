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
		$body = '';
		$settings = Model\Setting::fetchAll();
		$result = '';

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
	// public static function pages()
	// {
	// 	Result::pages();
	// }

	/**
	 * Show each page
	 *
	 * @return Void
	 */
	// public static function each()
	// {
	// 	Result::each(Input::get('url', ''));
	// }
}
