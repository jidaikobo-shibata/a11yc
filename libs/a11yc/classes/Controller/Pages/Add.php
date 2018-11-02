<?php
/**
 * A11yc\Controller\Pages\Add
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller\Pages;

use A11yc\Model;
use A11yc\Controller\Pages;

class Add
{
	/**
	 * add target pages
	 *
	 * @return Void
	 */
	public static function targetPages()
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
		Pages::count();

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
		$ua   = 'using';
		$type = 'raw';
		$html = Model\Html::getHtml($url, $ua, $type);
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

			// echo error message and continue
			if ( ! self::echoGetUrls($url, $urls, $base_url)) continue;

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
	 * echo getUrls messages
	 *
	 * @param  String $url
	 * @param  Array $urls
	 * @param  String $base_url
	 * @return Bool
	 */
	private static function echoGetUrls($url, $urls, $base_url)
	{
		// already added
		if (in_array($url, $urls))
		{
			echo "<strong style=\"color: #408000\">Already Added</strong>\n";
			return false;
		}

		// #
		if (strpos($url, '#') !== false)
		{
			echo "<strong style=\"color: #408000\">page fragment</strong>\n";
			return false;
		}

		// search from db
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Db::currentVersionSql().';';
		if (Db::fetch($sql, array($url)))
		{
			echo "<strong style=\"color: #408000\">Already exists</strong>\n";
			return false;
		}

		// is same host?
		if (mb_substr($url, 0, mb_strlen($base_url)) !== $base_url)
		{
			echo "<strong style=\"color: #408000\">Not in same host</strong>\n";
			return false;
		}

		// page not exists
		if ( ! Crawl::isPageExist($url))
		{
			echo "<strong style=\"color: #408000\">Page not exist</strong>\n";
			return false;
		}

		// page not exists
		if ( ! Crawl::isTargetMime($url))
		{
			echo "<strong style=\"color: #408000\">Not target webpage</strong>\n";
			return false;
		}
		return true;
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
	 * @param  Array $pages
	 * @return Void
	 */
	public static function addPages($is_force = false, $pages = array())
	{
		$pages = $pages ?: explode("\n", trim(Input::post('pages')));

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
			$from_internet = true;
			$title = Util::s(Model\Html::fetchPageTitle($url, $from_internet));
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
}
