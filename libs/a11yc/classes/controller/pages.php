<?php
/**
 * A11yc\Pages
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Controller_Pages
{
	/**
	 * Show Pages Index
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		$setup = Controller_Setup::fetch_setup();
		if ( ! $setup['target_level'])
		{
			Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
		}
		static::index();
	}

	/**
	 * dbio
	 *
	 * @return  void
	 */
	public static function dbio()
	{
		// update
		if (isset($_POST['pages']))
		{
			$pages = explode("\n", trim($_POST['pages']));
			$page_exists = false;

			ob_end_flush();
			ob_start('mb_output_handler');

			foreach ($pages as $k => $page)
			{
				$page = trim($page);
				if ( ! $page) continue;

				// is page exist?
				if ( ! Util::is_page_exist($page)) continue;
				if (Validate::is_ignorable($page)) continue;
				$page = Validate::correct_url($page);

				// do not check host.
				if (
					strpos($page, '#') !== false || // fragment
					! Util::is_html($page) // non html
				)
				{
					continue;
				}

				if ($k == 0)
				{
					$page_exists = true;
					echo '<div id="a11yc_add_pages_progress">'."\n";
				}

				// page title
				$pagetitle = Util::fetch_page_title($page);

				$page = urldecode($page);
				$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($page));
				if ( ! $exist)
				{
					$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
					$sql.= '(`url`, `trash`, `add_date`, `page_title`) VALUES (?, 0, ?, ?);';
					$r = Db::execute($sql, array($page, date('Y-m-d H:i:s'), $pagetitle));
				}

				echo Util::s($page).' ('.$k.'/'.count($pages).')<br />';
				echo Util::s($pagetitle).'<br />';

				ob_flush();
				flush();
			}

			if ($page_exists)
			{
				echo '</div>';
			}
		}

		// delete
		if (isset($_GET['del']))
		{
			$page = urldecode(trim($_GET['url']));
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 0;';
			$exist = Db::fetch($sql, array($page));
			if ($exist)
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 1 WHERE `url` = ?;';
				$r = Db::execute($sql, array($page));
			}
			else
			{
				if ( ! headers_sent()) header('location:'.A11YC_PAGES_URL);
			}
		}

		// undelete
		if (isset($_GET['undel']))
		{
			$page = urldecode(trim($_GET['url']));
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 1;';
			$exist = Db::fetch($sql, array($page));
			if ($exist)
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 0 WHERE `url` = ?;';
				$r = Db::execute($sql, array($page));
			}
			else
			{
				header('location:'.A11YC_PAGES_URL);
			}
		}

		// purge
		if (isset($_GET['purge']))
		{
			$page = urldecode(trim($_GET['url']));
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 1;';
			$exist = Db::fetch($sql, array($page));
			if ($exist)
			{
				$page = urldecode(trim($_GET['url']));
				$sql = 'DELETE FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;';
				$r = Db::execute($sql, array($page));
			}
			else
			{
				header('location:'.A11YC_PAGES_URL);
			}
		}

		if (isset($r))
		{
			if ($r)
			{
				Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
			}
		}
	}

	/**
	 * crawler
	 *
	 * param string $url
	 * @return  array
	 */
	public static function crawler($url, $recursive = false)
	{
		static $urls = array();

		// fetch attributes
		Validate::set_target_path($url);
		$html = Util::fetch_html($url);
		$html = Validate::ignore_elements($html);
		preg_match_all("/[ \n](?:href|action) *?= *?[\"']([^\"']+?)[\"']/i", $html, $ms);

		// host check
		if (substr_count($url, '/') >= 3)
		{
			$hosts = explode('/', $url);
			$host = $hosts[0].$hosts[1].'//'.$hosts[2];
		}
		else
		{
			$host = $url;
		}

		// collect url
		if ( ! isset($ms[1])) return false;

		ob_end_flush();
		ob_start('mb_output_handler');

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (Validate::is_ignorable($ms[0][$k])) continue;
			$url = Validate::correct_url($v);

			$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url));
			if ($exist) continue;

			if (in_array($url, $urls)) continue;
			$urls[$v] = $url;

			if ($k == 0)
			{
				echo '<div id="a11yc_add_pages_progress">'."\n";
			}

			echo Util::s($urls[$v]).' ('.$k.'/'.count($ms[1]).")<br />\n";

			if (
				strpos($urls[$v], '#') !== false || // fragment
				strpos($urls[$v], $host) === false || // out of host
				! Util::is_html($urls[$v]) // non html
			)
			{
				echo "ignored.<br />\n";
				unset($urls[$v]);
			}
			else
			{
				echo "<strong>added</strong>.<br />\n";
			}

			ob_flush();
			flush();
		}

		if ($urls)
		{
			echo '</div>';
		}

		// get urls recursive
		// if ( ! $recursive)
		// {
		// 	foreach ($urls as $k => $v)
		// 	{
		// 		if ($retvals = static::crawler($v, true))
		// 		{
		// 			$urls[] = array_merge($urls, $retvals);
		// 		}
		// 	}
		// }
		// $urls = array_unique($urls);

		return $urls;
	}

	/**
	 * get_urls
	 *
	 * @return  string
	 */
	public static function get_urls()
	{
		if ( ! isset($_POST['get_urls'])) return false;
		$url = $_POST['get_urls'];

		// html
		$urls = static::crawler($url);
		sort($urls);

		return $urls;
	}

	/**
	 * Manage Target Pages
	 *
	 * @return  string
	 */
	public static function index()
	{
		// dbio
		static::dbio();

		// pages
		$qs = '';
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE ';
		$list = isset($_GET['list']) ? $_GET['list'] : false;
		switch ($list)
		{
			case 'yet':
				$sql.= '`done` = 0 and `trash` = 0 ';
				$qs = '&amp;list=yet';
				break;
			case 'done':
				$sql.= '`done` = 1 and `trash` = 0 ';
				$qs = '&amp;list=done';
				break;
			case 'trash':
				$sql.= '`trash` = 1 ';
				$qs = '&amp;list=trash';
				break;
			default:
				$sql.= '`trash` = 0 ';
				break;
		}

		// search
		$word = '';
		if (isset($_GET['s']))
		{
			$word = mb_convert_kana(trim($_GET['s']), "as");
			if ($word)
			{
				$sql.= 'and (`url` LIKE ? OR `page_title` LIKE ?) ';
			}
		}

		// order
		if (
			isset($_GET['order']) &&
			in_array($_GET['order'], array('add_date_asc', 'add_date_desc', 'date_asc', 'date_desc', 'url_asc', 'url_desc', 'page_title_asc', 'page_title_desc'))
		)
		{
			$str = $_GET['order'];
			$order = strtoupper(substr($str, strrpos($str, '_') + 1));
			$by = strtolower(substr($str, 0, strrpos($str, '_')));
		}
		else
		{
			$order = 'DESC';
			$by = 'add_date';
		}
		$sql.= 'order by '.$by.' '.$order.';';

		// fetch
		if ($word)
		{
			$pages = Db::fetch_all($sql, array('%'.$word.'%', '%'.$word.'%'));
		}
		else
		{
			$pages = Db::fetch_all($sql);
		}

		// pagination
		$total = count($pages);
		$num = isset($_GET['num']) ? intval($_GET['num']) : 25 ;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1 ;

		// offset
		$offset = ($page - 1) * $num;
		$offset = $offset <= 0 ? 0 : $offset;

		// prev
		$prev = $page > 1 ? $page - 1 : false;
		$prev_qs = $prev ? '&amp;page='.$prev : '';

		// next
		$result = $offset + $num;
		$next = $total > $result ? $page + 1 : false;
		$next_qs = $next ? '&amp;page='.$next : '';

		// $pages
		$pages = array_slice($pages, $offset, $num);

		// assign
		View::assign('get_urls', isset($_POST['get_urls']) ? $_POST['get_urls'] : '');
		View::assign('crawled', static::get_urls());
		View::assign('pages', $pages);
		View::assign('prev', $prev_qs ? A11YC_PAGES_URL.$qs.$prev_qs : false);
		View::assign('next', $next_qs ? A11YC_PAGES_URL.$qs.$next_qs : false);
		View::assign('list', $list);
		View::assign('title', A11YC_LANG_PAGES_TITLE.' '.$list);
		View::assign('word', $word);
		View::assign('search_form', View::fetch_tpl('pages/search.php'), FALSE);
		View::assign('body', View::fetch_tpl('pages/index.php'), FALSE);
	}
}
