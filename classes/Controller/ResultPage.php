<?php
/**
 * A11yc\Controller\ResultPage
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait ResultPage
{
	/**
	 * show pages' list
	 *
	 * @param String $base_url
	 * @param Bool $is_center
	 * @return Void
	 */
	public static function page($base_url = '', $is_center = false)
	{
		$args = array(
			'list'   => 'done',
			'order'  => Input::get('order', 'seq_asc'),
		);

		$pdfs = array();
		$pages = array();
		foreach (array_keys(Values::selectionReasons()) as $k)
		{
			$args['reason'] = $k;
			$args['type']   = 1; // html
			$pages[$k] = Model\Page::fetchAll($args);

			$args['type'] = 2; // pdf
			$pdfs = array_merge($pdfs, Model\Page::fetchAll($args));
		}
		ksort($pages);

		$pages['pdf'] = $pdfs;

		// assign links
		static::assignLinks($base_url);

		View::assign('settings',  Model\Setting::fetchAll());
		View::assign('is_center', $is_center);
		View::assign('pages',     $pages);
		View::assign('title',     A11YC_LANG_CHECKED_PAGES);
		View::assign('body',      View::fetchTpl('result/page.php'), false);
	}
}
