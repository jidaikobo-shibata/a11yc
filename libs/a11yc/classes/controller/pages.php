<?php
/**
 * A11yc\Pages
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
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
	 * fetch page from db
	 *
	 * @param   string     $url
	 * @return  bool|array
	 */
	public static function fetch_page($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;';
		return Db::fetch($sql, array($url));
	}

	/**
	 * dbio
	 *
	 * @return  void
	 */
	public static function dbio()
	{
		// purge, undelete, delete
		if (Input::get('url'))
		{
			$url = Util::urldec(Input::get('url', ''));
			$page_title = Util::fetch_page_title_from_db($url);

			// purge
			static::dbio_purge($url, $page_title);

			// undelete
			static::dbio_undelete($url, $page_title);

			// delete
			static::dbio_delete($url, $page_title);
		}

		// update_pages
		static::dbio_update_pages();
	}

	/**
	 * dbio_delete
	 *
	 * @return  void
	 */
	private static function dbio_delete($url, $page_title)
	{
		// delete
		if (Input::get('del'))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ? and `trash` = 0;';
			if (Db::fetch($sql, array($url)))
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
	}

	/**
	 * dbio_undelete
	 *
	 * @return  void
	 */
	private static function dbio_undelete($url, $page_title)
	{
		if (Input::get('undel'))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ? and `trash` = 1;';
			if (Db::fetch($sql, array($url)))
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
	}

	/**
	 * dbio_purge
	 *
	 * @return  void
	 */
	private static function dbio_purge($url, $page_title)
	{
		if (Input::get('purge'))
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ? and `trash` = 1;';
			if (Db::fetch($sql, array($url)))
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
				Session::add(
					'messages',
					'errors',
					sprintf(A11YC_LANG_PAGES_PURGE_FAILED, Util::s($page_title.' ('.$url.') ')));
			}
		}
	}

	/**
	 * dbio_update_pages
	 *
	 * @return  void
	 */
	private static function dbio_update_pages()
	{
		// update add pages
		if (Input::post('pages'))
		{
			$pages = explode("\n", trim(Input::post('pages')));
			$pages = is_array($pages) ? $pages : array();

			// header
			if ( ! headers_sent())
			{
				header("HTTP/1.1 200 OK");
				header('Content-Type: text/html; charset=utf-8');
				echo '<!DOCTYPE html><html lang="'.A11YC_LANG.'"><head>';
				echo '<meta charset="utf-8">';
				echo '<title>'.A11YC_LANG_PAGES_URLS_ADD.' - A11YC</title></head><body>';
			}
			echo '<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>'."\n";
			echo '<script type="text/javascript" src="'.A11YC_URL_DIR.'/js/a11yc.js"></script>'."\n";
			echo '<link rel="stylesheet" type="text/css" media="all" href="'.A11YC_URL_DIR.'/css/a11yc.css" />'."\n";
			echo '<h1>'.A11YC_LANG_PAGES_ADD_TO_DATABASE.'</h1>'."\n";
			echo '<p>'.A11YC_LANG_PAGES_IT_TAKES_TIME.'</p><hr />'."\n";
			echo '<div style="word-break: break-all;">'."\n";
			echo '<script>a11yc_auto_scroll()</script>'."\n";

			ob_end_flush();
			ob_start('mb_output_handler');

			foreach ($pages as $k => $url)
			{
				// tidy url
				$url = Util::keep_url_unique($url);

				// fragment included
				if (strpos($url, '#') !== false) continue;

				// is page exist?
				if ( ! Util::is_page_exist($url))
				{
					Session::add(
						'messages',
						'errors',
						A11YC_LANG_PAGES_NOT_FOUND.': '. Util::s($page_title.' ('.$url.') '));
					continue;
				}

				// is html?
				if ( ! Util::is_html($url)) continue;

				// page title
				$page_title = Util::fetch_page_title($url);

				$current = $k + 1;
				echo '<p>'.Util::s($url).' ('.$current.'/'.count($pages).')<br />';
				echo Util::s($page_title).'<br />';

				if (Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url)))
				{
					Session::add(
						'messages',
						'errors',
						A11YC_LANG_PAGES_ALREADY_EXISTS.': '. Util::s($page_title.' ('.$url.') '));
					echo 'Already exists.';
					continue;
				}

				$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
				$sql.= '(`url`, `trash`, `add_date`, `page_title`) VALUES (?, 0, ?, ?);';
				$success = Db::execute($sql, array($url, date('Y-m-d H:i:s'), $page_title));
				if ($success)
				{
					Session::add(
						'messages',
						'messages',
						A11YC_LANG_PAGES_ADDED_NORMALLY.': '. Util::s($page_title.' ('.$url.') '));
					echo "<strong style=\"border-radius: 5px; padding: 5px; color: #fff;background-color:#408000;\">Added</strong>\n";
				}
				else
				{
					Session::add(
						'messages',
						'errors',
						A11YC_LANG_PAGES_ADD_FAILED.': '. Util::s($page_title.' ('.$url.') '));
					echo 'Failed.';
				}

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

	/**
	 * crawler
	 *
	 * param string $url
	 * @return  array
	 */
	public static function crawler($base_url)
	{
		static $urls = array();

		// fetch attributes
		$html = Util::fetch_html($base_url);
		$html = Validate::ignore_elements($html);
		preg_match_all("/[ \n](?:href|action) *?= *?[\"']([^\"']+?)[\"']/i", $html, $ms);

		// collect url
		if ( ! isset($ms[1])) return false;

		// header
		if ( ! headers_sent())
		{
			header("HTTP/1.1 200 OK");
			header('Content-Type: text/html; charset=utf-8');
			echo '<!DOCTYPE html><html lang="'.A11YC_LANG.'"><head>';
			echo '<meta charset="utf-8">';
			echo '<title>'.A11YC_LANG_PAGES_GET_URLS.' - A11YC</title></head><body>';
		}
		echo '<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.A11YC_URL_DIR.'/js/a11yc.js"></script>'."\n";
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.A11YC_URL_DIR.'/css/a11yc.css" />'."\n";

		echo '<div style="word-break: break-all;">'."\n";
		echo '<script>a11yc_auto_scroll()</script>'."\n";
		echo '<h1>'.A11YC_LANG_PAGES_ADD_TO_CANDIDATE.'</h1>'."\n";
		echo '<p>'.A11YC_LANG_PAGES_IT_TAKES_TIME.'</p><hr />'."\n";

		// draw
		ob_end_flush();
		ob_start('mb_output_handler');

		$urls = array();
		foreach ($ms[1] as $k => $url)
		{
			// tidy url
			$url = Util::keep_url_unique($url);

			// results
			$current = $k + 1;
			echo '<p>'.Util::s($url).' ('.$current.'/'.count($ms[1]).")<br />\n";

			// messages
			if (
				// already added
				in_array($url, $urls) ||
				// already in db
				Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;', array($url))
			)
			{
				echo "<strong style=\"color: #408000\">Already exists</strong>\n";
			}
			else if (
				strpos($url, '#') !== false || // fragment included
				! Util::is_same_host($base_url, $url) || // out of host
				! Util::is_page_exist($url) || // page not exists
				! Util::is_html($url) // non html
			)
			{
				echo "<strong>Ignored</strong>\n";
			}
			else
			{
				$urls[] = $url;
				echo "<strong style=\"border-radius: 5px; padding: 5px; color: #fff;background-color:#408000;\">Add to candidate</strong>\n";
			}
			echo '</p>';

			ob_flush();
			flush();
		}

		echo '</div>';

		// add to session
		// sort($urls); // yousu-mi
		Session::add('values', 'urls', $urls);

		// done
		if (count($urls) === 0)
		{
			echo '<p><a id="a11yc_back_to_pages" href="'.A11YC_PAGES_URL.'">'.A11YC_LANG_PAGES_NOT_FOUND_ALL.'</a></p>';

			$errs = Session::fetch('messages', 'errors');
			if ($errs)
			{
				foreach ($errs as $err)
				{
					echo '<p>'.$err.'</p>';
				}
			}
			if (strpos($base_url, 'https:') !== false)
			{
				echo '<p>'.A11YC_LANG_PAGES_NOT_FOUND_SSL.'</p>';
			}
		}
		else
		{
			echo '<p><a id="a11yc_back_to_pages" href="'.A11YC_PAGES_URL.'">'.A11YC_LANG_PAGES_RETURN_TO_PAGES.'</a></p>';
		}
		echo '<script>a11yc_stop_scroll()</script>'."\n";

		if ( ! headers_sent())
		{
			echo '</body>';
		}
		exit();
	}

	/**
	 * get_urls
	 *
	 * @return  string
	 */
	public static function get_urls()
	{
		if ( ! Input::post('get_urls')) return false;
		$url = Input::post('get_urls');
		Session::add('param', 'get_urls', $url);
		static::crawler($url);
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
		$list = Input::get('list', false);
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

		// order
		$order = 'DESC';
		$by    = 'add_date';

		$order_whitelist = array(
			'add_date_asc',
			'add_date_desc',
			'date_asc',
			'date_desc',
			'url_asc',
			'url_desc',
			'page_title_asc',
			'page_title_desc');
		$orderby = Input::get('order', false);
		if (in_array($orderby, $order_whitelist))
		{
			$order = strtoupper(substr($orderby, strrpos($orderby, '_') + 1));
			$by    = strtolower(substr($orderby, 0, strrpos($orderby, '_')));
		}
		$sql_odr = 'order by '.$by.' '.$order.';';

		// fetch
		$word = '';
		if (Input::get('s'))
		{
			$word = mb_convert_kana(trim(Input::get('s')), "as");
			$sql.= $word ? 'and (`url` LIKE ? OR `page_title` LIKE ?) ' : '';
			$pages = Db::fetch_all($sql.$sql_odr, array('%'.$word.'%', '%'.$word.'%'));
		}
		else
		{
			$pages = Db::fetch_all($sql.$sql_odr);
		}

		// pagination
		$total = count($pages);
		$num = Input::get('num') ? intval(Input::get('num')) : 25 ;
		$paged = Input::get('paged') ? intval(Input::get('paged')) : 1 ;

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

		// crawled urls
		$from_session = Session::fetch('values', 'urls');
		$crawled = '';
		$get_urls = '';
		if (isset($from_session[0]))
		{
			$crawled = $from_session[0];
			Session::add('messages', 'message', A11YC_LANG_PAGES_PRESS_ADD_BUTTON);
			$get_urls_session = Session::fetch('param', 'get_urls');
			$get_urls = Arr::get($get_urls_session, 0, '');
		}
		else
		{
			static::get_urls();
		}

		// assign
		View::assign('crawled', $crawled);
		View::assign('get_urls', $get_urls);
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
