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
	 * @return Void
	 */
	public static function page()
	{
		$args = array(
			'list'   => 'done',
			'order'  => Input::get('order', 'url_asc'),
		);

		$pdfs = array();
		$pages = array();
		foreach (array_keys(Values::selectionReasons()) as $k)
		{
			$args['reason'] = $k;
			$args['type']   = 'html';
			$pages[$k] = Model\Page::fetchAll($args);

			$args['type'] = 'pdf';
			$pdfs = array_merge($pdfs, Model\Page::fetchAll($args));
		}
		ksort($pages);

		$pages['pdf'] = $pdfs;

		// assign links
		static::assignLinks();

		View::assign('settings', Model\Setting::fetchAll());
		View::assign('selection_reasons', Values::selectionReasons());
		View::assign('pages', $pages);
		View::assign('title', A11YC_LANG_CHECKED_PAGES);
		View::assign('body', View::fetchTpl('result/page.php'), false);
		return;
	}
}
