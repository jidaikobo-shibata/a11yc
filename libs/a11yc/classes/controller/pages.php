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
		$list = isset($_GET['list']) ? $_GET['list'] : false;
		switch ($list)
		{
			case 'yet':
				$pages = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 0 and `trash` = 0 ORDER BY `url` ASC;');
				break;
			case 'done':
				$pages = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0 ORDER BY `url` ASC;');
				break;
			case 'trash':
				$pages = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 1 ORDER BY `url` ASC;');
				break;
			default:
				$pages = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0 ORDER BY `url` ASC;');
				break;
		}

		// assign
		View::assign('pages', $pages);
		View::assign('list', $list);
		View::assign('title', A11YC_LANG_PAGES_TITLE);
		View::assign('body', View::fetch_tpl('pages/index.php'), FALSE);
	}
}
