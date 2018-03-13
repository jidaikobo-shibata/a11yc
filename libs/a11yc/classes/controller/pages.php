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
		static::add();
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
	private static function count()
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

		// count
		View::assign('list', $list);
		static::count();

		// assign
		View::assign('title',       A11YC_LANG_PAGES_TITLE.' '.$list);
		View::assign('settings',    Model\Settings::fetchAll());
		View::assign('pages',       Model\Pages::fetch($args));
		View::assign('word',        join(' ', $words));
		View::assign('search_form', View::fetchTpl('pages/inc_search.php'), FALSE);
		View::assign('body',        View::fetchTpl('pages/index.php'), FALSE);
	}

	/**
	 * add target pages
	 *
	 * @return Void
	 */
	public static function add()
	{
		$get_urls = Util::urldec(Input::post('get_urls'));
		$crawled = Session::fetch('values', 'urls');
		$crawled = is_array($crawled[0]) ? join("\n", $crawled[0]) : '';
		$is_force = Input::post('force', false);

		if (Input::isPostExists())
		{
			if ($get_urls)
			{
				$crawled = self::getUrls($get_urls);
				Session::add('param', 'get_urls', $get_urls);
			}
			else
			{
				self::addPages($is_force);
			}
		}

		// count
		View::assign('list', 'add');
		static::count();

		View::assign('crawled', $crawled);
		View::assign('get_urls', $get_urls);
		View::assign('title', A11YC_LANG_CTRL_ADDNEW);
		View::assign('body',  View::fetchTpl('pages/add.php'), FALSE);
	}

	/**
	 * add target pages
	 *
	 * @param  string $url
	 * @return array
	 */
	private static function getUrls($url)
	{
		// fetch attributes
		$html = Model\Html::getHtml($url, $ua = 'using', $type = 'raw');
		preg_match_all("/[ \n](?:href|action) *?= *?[\"']([^\"']+?)[\"']/i", $html, $ms);

		// collect url
		if ( ! isset($ms[1])) return false;

		// draw
		ob_end_flush();
		ob_start('mb_output_handler');

		// header
		View::assign('title', A11YC_LANG_PAGES_ADD_TO_CANDIDATE);
		echo View::fetchTpl('inc_progress_header.php');

		// adding
		$urls = array();
		$base_url = Arr::get(Model\Settings::fetchAll(), 'base_url');
		foreach ($ms[1] as $k => $orig_url)
		{
			$url = Util::enuniqueUri($orig_url);

			$current = $k + 1;
			echo '<p>'.Util::s($url).' ('.Util::s($orig_url).': '.$current.'/'.count($ms[1]).")<br />\n";

			// already added
			if (in_array($url, $urls))
			{
				echo "<strong style=\"color: #408000\">Already Added</strong>\n";
				continue;
			}

			// #
			if ($url[0] == '#')
			{
				echo "<strong style=\"color: #408000\">page fragment</strong>\n";
				continue;
			}

			// search from db
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Db::currentVersionSql().';';
			if (Db::fetch($sql, array($url)))
			{
				echo "<strong style=\"color: #408000\">Already exists</strong>\n";
				continue;
			}

			// is same host?
			if (mb_substr($url, 0, mb_strlen($base_url)) !== $base_url)
			{
				echo "<strong style=\"color: #408000\">Not in same host</strong>\n";
				continue;
			}

			// page not exists
			if ( ! Crawl::isPageExist($url))
			{
				echo "<strong style=\"color: #408000\">Page not exist</strong>\n";
				continue;
			}

			// page not exists
			if ( ! Crawl::isTargetMime($url))
			{
				echo "<strong style=\"color: #408000\">Not target webpage</strong>\n";
				continue;
			}

			echo "<strong style=\"border-radius: 5px; padding: 5px; color: #fff;background-color:#408000;\">Add to candidate</strong>\n";

			echo '</p>';

			ob_flush();
			flush();

			$urls[] = $url;
		}

		echo '</div>';
		echo '</div>';

		// add to session
		Session::remove('values', 'urls');
		Session::add('values', 'urls', $urls);

		// done
		if (count($urls) === 0)
		{
			echo '<p><a id="a11yc_back_to_pages" href="'.A11YC_PAGES_URL.'">'.A11YC_LANG_PAGES_NOT_FOUND_ALL.'</a></p>';

			if (strpos($base_url, 'https:') !== false)
			{
				echo '<p>'.A11YC_LANG_PAGES_NOT_FOUND_SSL.'</p>';
			}
		}
		else
		{
			echo '<p><a id="a11yc_back_to_pages" href="'.A11YC_PAGES_ADD_URL.'">'.A11YC_LANG_PAGES_RETURN_TO_PAGES.'</a></p>';
		}
		echo '<script>a11yc_stop_scroll()</script>'."\n";

		if ( ! headers_sent())
		{
			echo '</body>';
		}
		exit();
	}

	/**
	 * addPageMessage
	 *
	 * @param  Bool $is_success
	 * @param  String $url
	 * @param  String $title
	 * @return Void
	 */
	private static function addPageMessage($is_success, $url = '', $title = '')
	{
		if ($is_success)
		{
			Session::add(
				'messages',
				'messages',
				A11YC_LANG_PAGES_ADDED_NORMALLY.': '. Util::s($title.' ('.$url.') '));
		}
		else
		{
			Session::add(
				'messages',
				'errors',
				A11YC_LANG_PAGES_ADD_FAILED.': '. Util::s($title.' ('.$url.') '));
		}
	}

/**
 * addPages
 *
 * @param  Bool $is_force
 * @return Void
 */
private static function addPages($is_force = false)
	{
		$pages = explode("\n", trim(Input::post('pages')));

		// add without check
		if ( ! Guzzle::envCheck() || $is_force)
		{
			foreach ($pages as $url)
			{
				$url = trim($url);
				if (empty($url)) continue;
				self::addPageMessage(Model\Pages::addPage($url), $url);
			}
			return;
		}

		// use Guzzle
		ob_end_flush();
		ob_start('mb_output_handler');

		// header
		View::assign('title', A11YC_LANG_PAGES_ADD_TO_DATABASE);
		echo View::fetchTpl('inc_progress_header.php');

		foreach ($pages as $k => $url)
		{
			$url = trim($url);
			if ( ! $url) continue;

			// tidy url
			$url = Util::enuniqueUri($url);

			// fragment included
			if (strpos($url, '#') !== false) continue;

			// is page exist?
			if ( ! Crawl::isPageExist($url))
			{
				Session::add(
					'messages',
					'errors',
					A11YC_LANG_PAGES_NOT_FOUND.': '. Util::s($url));
				continue;
			}

			// is in target mime?
			if ( ! Crawl::isTargetMime($url)) continue;

			$current = $k + 1;
			$title = Util::s(Model\Html::fetchPageTitle($url, $from_internet = true));
			echo '<p>'.Util::s($url).' ('.$current.'/'.count($pages).')<br />';
			echo $title.'<br />';
			echo "<strong style=\"border-radius: 5px; padding: 5px; color: #fff;background-color:#408000;\">Add</strong>\n";

			self::addPageMessage(Model\Pages::addPage($url), $url, $title);

			echo '</p>';

			ob_flush();
			flush();
		}

		echo '</div>';
		// done
		echo '<p><a id="a11yc_back_to_pages" href="'.A11YC_PAGES_URL.'">'.A11YC_LANG_PAGES_DONE.'</a></p>';
		echo '<script>a11yc_stop_scroll()</script>'."\n";
		if ( ! headers_sent())
		{
			echo '</body>';
		}
		exit();
	}

	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function edit()
	{
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

				case 'result' :
					break;

				case 'live' :
					break;

				case 'image' :
					break;

				case 'export' :
					break;

				case 'delete' :
					$is_success = Model\Pages::delete($url);
					$message = $is_success ? A11YC_LANG_PAGES_DELETE_DONE : A11YC_LANG_PAGES_DELETE_FAILED;
					$redirect_to = A11YC_PAGES_URL;
					break;

				case 'undelete' :
					$is_success = Model\Pages::undelete($url);
					$message = $is_success ? A11YC_LANG_PAGES_UNDELETE_DONE : A11YC_LANG_PAGES_UNDELETE_FAILED;
					$redirect_to = A11YC_PAGES_URL;
					break;

				case 'purge' :
					$is_success = Model\Pages::purge($url);
					Model\Checklist::delete($url);
					$message = $is_success ? A11YC_LANG_PAGES_PURGE_DONE : A11YC_LANG_PAGES_PURGE_FAILED;
					$redirect_to = A11YC_PAGES_URL;
					break;

				default :
					Model\Pages::updateField($url, 'title', Input::post('title'));
					Model\Html::addHtml($url, $ua = 'using', Input::post('html'));
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
		$page = Model\Pages::fetchPage($url, $force = 1);
		if ( ! $page) Util::error('Page not found');

		$html = Model\Html::getHtml($url);

		View::assign('list', 'all');
		static::count();
		View::assign('url',   Util::urlenc($url));
		View::assign('title', A11YC_LANG_PAGES_LABEL_EDIT);
		View::assign('page_title', isset($page['title']) ? $page['title'] : '');
		View::assign('html',  $html);
		View::assign('body',  View::fetchTpl('pages/edit.php'), FALSE);
	}

}
