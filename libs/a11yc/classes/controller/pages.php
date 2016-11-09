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
		// update add pages
		if (isset($_POST['pages']))
		{
			$pages = explode("\n", trim($_POST['pages']));
			$page_exists = false;

			ob_end_flush();
			ob_start('mb_output_handler');

			foreach ($pages as $k => $url)
			{
				$url = trim($url);
				if ( ! $url) continue;

				// is page exist?
				if ( ! Util::is_page_exist($url))
				{
					Session::add(
						'messages',
						'errors',
						A11YC_LANG_PAGES_NOT_FOUND.': '. Util::s($page_title.' ('.$url.') '));
					continue;
				}
				if (Validate::is_ignorable($url)) continue;
				$url = Validate::correct_url($url);

				// do not check host.
				if (
					strpos($url, '#') !== false || // fragment
					! Util::is_html($url) // non html
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
				$page_title = Util::fetch_page_title($url);

				$url = urldecode($url);
				$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url));
				if ( ! $exist)
				{
					$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
					$sql.= '(`url`, `trash`, `add_date`, `page_title`) VALUES (?, 0, ?, ?);';
					$success = Db::execute($sql, array($url, date('Y-m-d H:i:s'), $page_title));
					if ($success)
					{
						Session::add(
							'messages',
							'messages',
							A11YC_LANG_PAGES_ADDED_NORMALLY.': '. Util::s($page_title.' ('.$url.') '));
					}
					else
					{
						Session::add(
							'messages',
							'errors',
							A11YC_LANG_PAGES_ADD_FAILED.': '. Util::s($page_title.' ('.$url.') '));
					}
				}
				else
				{
					Session::add(
						'messages',
						'errors',
						A11YC_LANG_PAGES_ALREADY_EXISTS.': '. Util::s($page_title.' ('.$url.') '));
				}

				echo Util::s($page).' ('.$k.'/'.count($pages).')<br />';
				echo Util::s($page_title).'<br />';

				ob_flush();
				flush();
			}

			if ($page_exists)
			{
				echo '</div>';
			}
		}

		// page_title and urldecode
		if (isset($_GET['url']))
		{
			$url = urldecode(trim($_GET['url']));
			$url = str_replace('&amp;', '&', $url);
			$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url));
			if ($exist)
			{
				$page_title = $exist['page_title'];
			}
		}

		// delete
		if (isset($_GET['del']))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 0;';
			$exist = Db::fetch($sql, array($url));

			if ($exist)
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 1 WHERE `url` = ?;';
				Db::execute($sql, array($url));
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_DELETE_DONE, Util::s($page_title.' ('.$url.') ')));
			}
			else
			{
				Session::add(
					'messages',
					'errors',
					sprintf(A11YC_LANG_PAGES_DELETE_FAILED, Util::s($page_title.' ('.$url.') ')));
			}
		}

		// undelete
		if (isset($_GET['undel']))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 1;';
			$exist = Db::fetch($sql, array($url));
			if ($exist)
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 0 WHERE `url` = ?;';
				Db::execute($sql, array($url));
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_UNDELETE_DONE, Util::s($page_title.' ('.$url.') ')));
			}
			else
			{
				Session::add(
					'messages',
					'errors',
					sprintf(A11YC_LANG_PAGES_UNDELETE_FAILED, Util::s($page_title.' ('.$url.') ')));
			}
		}

		// purge
		if (isset($_GET['purge']))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
			$sql.= ' WHERE `url` = ? and `trash` = 1;';
			$exist = Db::fetch($sql, array($url));
			if ($exist)
			{
				$sql = 'DELETE FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;';
				Db::execute($sql, array($url));
				Session::add(
					'messages',
					'messages',
					sprintf(A11YC_LANG_PAGES_PURGE_DONE, Util::s($page_title.' ('.$url.') ')));
			}
			else
			{
//				header('location:'.A11YC_PAGES_URL);
				Session::add(
					'messages',
					'errors',
					sprintf(A11YC_LANG_PAGES_PURGE_FAILED, Util::s($page_title.' ('.$url.') ')));
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
		$yetwhr   = '`done` = 0 and `trash` = 0 ';
		$donewhr  = '`done` = 1 and `trash` = 0 ';
		$trashwhr = '`trash` = 1 ';
		$allwhr   = '`trash` = 0 ';
		switch ($list)
		{
			case 'yet':
				$sql.= $yetwhr;
				$qs = '&amp;list=yet';
				break;
			case 'done':
				$sql.= $donewhr;
				$qs = '&amp;list=done';
				break;
			case 'trash':
				$sql.= $trashwhr;
				$qs = '&amp;list=trash';
				break;
			default:
				$sql.= $allwhr;
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
		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1 ;

		// offset
		$offset = ($paged - 1) * $num;
		$offset = $offset <= 0 ? 0 : $offset;

		// prev
		$prev = $paged > 1 ? $paged - 1 : false;
		$prev_qs = $prev ? '&amp;paged='.$prev : '';

		// next
		$result = $offset + $num;
		$next = $total > $result ? $paged + 1 : false;
		$next_qs = $next ? '&amp;paged='.$next : '';

		// $pages
		$pages = array_slice($pages, $offset, $num);

		// consists num by query string
		$qs.= '&amp;num='.$num; // done with intval()

		// count
		$cntsql = 'SELECT count(url) AS num FROM '.A11YC_TABLE_PAGES.' WHERE ';

		// assign index information
		$end_num = $total > $result ? $offset + $num : $total;
		View::assign(
			'index_information',
			sprintf(A11YC_LANG_PAGES_INDEX_INFORMATION, count($pages), $total, $offset + 1, $end_num)
		);

		// assign nums
		View::assign('yetcnt', Db::fetch($cntsql.$yetwhr));
		View::assign('donecnt', Db::fetch($cntsql.$donewhr));
		View::assign('trashcnt', Db::fetch($cntsql.$trashwhr));
		View::assign('allcnt', Db::fetch($cntsql.$allwhr));

		// assign
		View::assign('get_urls', isset($_POST['get_urls']) ? $_POST['get_urls'] : '');
		View::assign('crawled', static::get_urls());
		View::assign('pages', $pages);
		View::assign('current_qs', $qs.'&amp;paged='.$paged.'&amp;s='.$word);
		View::assign('prev', $prev_qs ? A11YC_PAGES_URL.$qs.$prev_qs : false);
		View::assign('next', $next_qs ? A11YC_PAGES_URL.$qs.$next_qs : false);
		View::assign('list', $list);
		View::assign('title', A11YC_LANG_PAGES_TITLE.' '.$list);
		View::assign('word', $word);
		View::assign('search_form', View::fetch_tpl('pages/search.php'), FALSE);
		View::assign('body', View::fetch_tpl('pages/index.php'), FALSE);
	}
}
