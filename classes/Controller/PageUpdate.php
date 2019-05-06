<?php
/**
 * A11yc\Controller\PageUpdate
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait PageUpdate
{
	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function edit()
	{
		$url_path = base64_encode(Input::get('url'));
		$url = Util::urldec(Input::get('url'));

		if (Input::isPostExists())
		{
			Model\Page::updatePartial($url, 'title', Input::post('title'));
			Model\Page::updatePartial($url, 'serial_num', intval(Input::post('serial_num')));
			Util::setMassage(Model\Page::updatePartial($url, 'seq', intval(Input::post('seq'))));
			$ua = 'using';
			Model\Html::insert($url, Input::post('html'), $ua);
			$page = Model\Page::fetch($url);
			$newfilename = File::uploadImg('pages', $url_path, Arr::get($page, 'image_path'));
			Model\Page::updatePartial($url, 'image_path', $newfilename);
			Util::redirect(A11YC_PAGE_URL.'edit&amp;url='.Util::urlenc($url));
		}

		// show edit page
		$force = true;
		$page = Model\Page::fetch($url, $force);
		if ( ! $page) Util::error('Page not found');

		$html = Model\Html::fetch($url);

		View::assign('list', 'all');
		self::count();
		View::assign('url', Util::urlenc($url));
		View::assign('url_path', $url_path);
		View::assign('title', A11YC_LANG_CTRL_LABEL_EDIT);
		View::assign('page_title', isset($page['title']) ? $page['title'] : '');
		View::assign('page',  $page);
		View::assign('html',  $html);
		View::assign('body',  View::fetchTpl('page/edit.php'), FALSE);
	}

	/**
	 * updateHtml
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function updateHtml($url)
	{
		$referer = Input::server('HTTP_REFERER');
		if (substr($referer, 0, strlen(A11YC_URL)) != A11YC_URL) Util::error('wrong request');
		$html = Model\Html::fetchHtmlFromInternet($url);

		Util::setMassage(Model\Html::insert($url, $html));
		Util::redirect($referer);
	}
}
