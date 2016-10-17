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
			foreach ($pages as $page)
			{
				$page = trim($page);
				if ( ! $page) continue;

				// is page exist?
				if ( ! Util::is_page_exist($page)) continue;

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
				header('location:'.A11YC_PAGES_URL);
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
			in_array($_GET['order'], array('add_date_asc', 'add_date_desc', 'date_asc', 'date_desc', 'url_asc', 'url_desc', 'name_asc', 'name_desc'))
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
