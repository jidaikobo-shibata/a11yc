<?php
/**
 * A11yc\Controller\Pages
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Pages
{
	/**
	 * Show Pages Index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * add Pages
	 *
	 * @return Void
	 */
	public static function actionAdd()
	{
		Pages\Add::targetPages();
	}

	/**
	 * edit Page
	 *
	 * @return Void
	 */
	public static function actionEdit()
	{
		static::edit();
	}

	/**
	 * count pages
	 *
	 * @return Void
	 */
	public static function count()
	{
		$count = array(
			'all'   => Model\Pages::count('all'),
			'yet'   => Model\Pages::count('yet'),
			'done'  => Model\Pages::count('done'),
			'trash' => Model\Pages::count('trash'),
		);

		View::assign('count', $count);
		View::assign('submenu', View::fetchTpl('pages/inc_submenu.php'), FALSE);
	}

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
			'order'   => Input::get('order', 'created_at_desc'),
		);

		if (Input::isPostExists())
		{
			switch (Input::post('operation'))
			{
				case 'delete':
					Pages\Bulk::delete();
					break;
				case 'undelete':
					Pages\Bulk::undelete();
					break;
				case 'purge':
					Pages\Bulk::purge();
					break;
				case 'result':
					Pages\Bulk::result();
					break;
				case 'export':
					Export::csv(array_keys(Input::postArr('bulk')));
					break;
				default: // update order
					Pages\Bulk::updateSeq();
					break;
			}
		}

		// count
		View::assign('list', $list);
		self::count();

		// assign
		View::assign('title',       A11YC_LANG_PAGES_TITLE.' '.$list);
		View::assign('settings',    Model\Settings::fetchAll());
		View::assign('pages',       Model\Pages::fetch($args));
		View::assign('word',        join(' ', $words));
		View::assign('search_form', View::fetchTpl('pages/inc_search.php'), FALSE);
		View::assign('body',        View::fetchTpl('pages/index.php'), FALSE);
	}

	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function edit()
	{
		$url_path = base64_encode(Input::get('url'));
		$url = Util::urldec(Input::get('url'));
		$is_success = false;
		$redirect_to = '';
		$message = '';

		if (Input::isPostExists())
		{
			switch (Input::post('operation'))
			{
				case 'check' :
					$redirect_to = A11YC_CHECKLIST_URL.Util::urlenc($url);
					break;

				case 'delete' :
					$is_success = Model\Pages::delete($url);
					$message = $is_success ? A11YC_LANG_PAGES_DELETE_DONE : A11YC_LANG_PAGES_DELETE_FAILED;
					$redirect_to = A11YC_PAGES_URL.'index';
					break;

				case 'undelete' :
					$is_success = Model\Pages::undelete($url);
					$message = $is_success ? A11YC_LANG_PAGES_UNDELETE_DONE : A11YC_LANG_PAGES_UNDELETE_FAILED;
					$redirect_to = A11YC_PAGES_URL.'index';
					break;

				case 'purge' :
					$is_success = Model\Pages::purge($url);
					Model\Checklist::delete($url);
					$message = $is_success ? A11YC_LANG_PAGES_PURGE_DONE : A11YC_LANG_PAGES_PURGE_FAILED;
					$redirect_to = A11YC_PAGES_URL.'index';
					break;

				default :
					Model\Pages::updateField($url, 'title', Input::post('title'));
					Model\Pages::updateField($url, 'seq', intval(Input::post('seq')));
					$ua = 'using';
					Model\Html::addHtml($url, $ua, Input::post('html'));
					$page = Model\Pages::fetchPage($url);
					$newfilename = Upload::img('pages', $url_path, $page['image_path']);
					Model\Pages::updateField($url, 'image_path', $newfilename);

					break;
			}
		}

		// redirect?
		if ($redirect_to)
		{
			if ($message)
			{
				$mess_type = $is_success ? 'messages' : 'errors' ;
				Session::add('messages', $mess_type, sprintf($message, Util::s($url)));
			}
			Util::redirect($redirect_to);
		}

		// show edit page
		$force = true;
		$page = Model\Pages::fetchPage($url, $force);
		if ( ! $page) Util::error('Page not found');

		$html = Model\Html::getHtml($url);

		View::assign('list', 'all');
		self::count();
		View::assign('url', Util::urlenc($url));
		View::assign('url_path', $url_path);
		View::assign('title', A11YC_LANG_PAGES_LABEL_EDIT);
		View::assign('page_title', isset($page['title']) ? $page['title'] : '');
		View::assign('page',  $page);
		View::assign('html',  $html);
		View::assign('body',  View::fetchTpl('pages/edit.php'), FALSE);
	}

}
