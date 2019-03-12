<?php
/**
 * A11yc\Controller\PageIndex
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait PageIndex
{
	/**
	 * Manage Target Pages
	 *
	 * @return Void
	 */
	public static function index()
	{
		// fetch pages
		$list  = Input::get('list', 'all');
		$words = Util::searchWords2Arr(Input::get('s', ''));
		$args = array(
			'list'    => $list,
			'words'   => $words,
			'order'   => Input::get('order', 'seq_asc'),
		);

		if (Input::isPostExists())
		{
			switch (Input::post('operation'))
			{
				case 'delete':
					static::delete();
					break;
				case 'undelete':
					static::undelete();
					break;
				case 'purge':
					static::purge();
					break;
				default: // update order and id
					static::updateSeq();
					static::updateSerialNum();
					break;
			}
			Util::redirect(A11YC_PAGE_URL.'index');
		}

		// count
		View::assign('list', $list);
		self::count();

		// assign
		View::assign('title',       A11YC_LANG_PAGE_TITLE.' '.constant('A11YC_LANG_CTRL_'.strtoupper($list)));
		View::assign('settings',    Model\Setting::fetchAll());
		View::assign('pages',       Model\Page::fetchAll($args));
		View::assign('word',        join(' ', $words));
		View::assign('search_form', View::fetchTpl('page/inc_search.php'), FALSE);
		View::assign('body',        View::fetchTpl('page/index.php'), FALSE);
	}
}
