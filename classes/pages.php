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
class Pages
{
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
				$page = Db::escapeStr($page);
				$exist = Db::fetch('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = '.$page.';');
				if ( ! $exist)
				{
					Db::execute('INSERT INTO '.A11YC_TABLE_PAGES.' (`url`, `trash`) VALUES ('.$page.', 0);');
				}
			}
		}

		// delete
		if (isset($_GET['del']))
		{
			$page = urldecode(trim($_GET['url']));
			$page = Db::escapeStr($page);
			Db::execute('UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 1 WHERE `url` = '.$page.';');
		}

		// undelete
		if (isset($_GET['undel']))
		{
			$page = urldecode(trim($_GET['url']));
			$page = Db::escapeStr($page);
			Db::execute('UPDATE '.A11YC_TABLE_PAGES.' SET `trash` = 0 WHERE `url` = '.$page.';');
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
				$pages = Db::fetchAll('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 0 and `trash` = 0;');
				break;
			case 'done':
				$pages = Db::fetchAll('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0;');
				break;
			case 'trash':
				$pages = Db::fetchAll('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 1;');
				break;
			default:
				$pages = Db::fetchAll('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0;');
				break;
		}

		// assign
		\A11yc\View::assign('pages', $pages);
		\A11yc\View::assign('list', $list);
		\A11yc\View::assign('title', A11YC_LANG_PAGES_TITLE);
		\A11yc\View::assign('body', \A11yc\View::fetch_tpl('pages/index.php'), FALSE);
	}
}
