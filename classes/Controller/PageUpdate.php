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
					$is_success = Model\Page::delete($url);
					$message = $is_success ? A11YC_LANG_CTRL_DELETE_DONE : A11YC_LANG_CTRL_DELETE_FAILED;
					$redirect_to = A11YC_PAGE_URL.'index';
					break;

				case 'undelete' :
					$is_success = Model\Page::undelete($url);
					$message = $is_success ? A11YC_LANG_CTRL_UNDELETE_DONE : A11YC_LANG_CTRL_UNDELETE_FAILED;
					$redirect_to = A11YC_PAGE_URL.'index';
					break;

				case 'purge' :
					$is_success = Model\Page::purge($url);
					Model\Checklist::delete($url);
					$message = $is_success ? A11YC_LANG_CTRL_PURGE_DONE : A11YC_LANG_CTRL_PURGE_FAILED;
					$redirect_to = A11YC_PAGE_URL.'index';
					break;

				default :
					Model\Page::updatePartial($url, 'title', Input::post('title'));
					Model\Page::updatePartial($url, 'seq', intval(Input::post('seq')));
					$ua = 'using';
					Model\Html::insert($url, $ua, Input::post('html'));
					$page = Model\Page::fetch($url);
					$newfilename = File::uploadImg('pages', $url_path, Arr::get($page, 'image_path'));
					Model\Page::updatePartial($url, 'image_path', $newfilename);

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

}
