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

		// form
		$html = '';
		$html.= '<form action="" method="POST">';
		$html.= '<h2><label for="a11yc_pages">'.A11YC_LANG_PAGES_URLS.'</label></h2>';
		$html.= '<textarea id="a11yc_pages" name="pages" rows="7" style="width: 100%;">';
		$html.= '</textarea>';
		$html.= '<input type="submit" value="'.A11YC_LANG_PAGES_URLS_ADD.'" />';
		$html.= '</form>';

		// list
		$html.= '<h2>'.A11YC_LANG_PAGES_TITLE.'</h2>';
		$html.= '<p><a href="'.A11YC_PAGES_URL.'">pages</a> | ';
		$html.= '<a href="'.A11YC_PAGES_URL.'&amp;list=yet">yet</a> | ';
		$html.= '<a href="'.A11YC_PAGES_URL.'&amp;list=done">done</a> | ';
		$html.= '<a href="'.A11YC_PAGES_URL.'&amp;list=trash">trash</a></p>';

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

		if ($pages)
		{
			$html.= '<table class="a11yc_tbl">';
			$html.= '<thead>';
			$html.= '<th>URL</th>';
			$html.= '<th>Level</th>';
			$html.= '<th>Done</th>';
			$html.= '<th>Check</th>';
			$html.= '<th>Delete</th>';
			$html.= '</thead>';
			foreach ($pages as $page)
			{
				$url = Util::s($page['url']);
				$html.= '<tr>';
				$html.= '<th>'.$url.'</th>';
				$html.= '<td>'.Util::num2str($page['level']).'</td>';
				$done = @$page['done'] == 1 ? 'Done' : '' ;
				$html.= '<td>'.$done.'</td>';
				$html.= '<td><a href="'.A11YC_CHECKLIST_URL.urlencode($url).'">Check</a></td>';
				if ($list == 'trash')
				{
					$html.= '<td><a href="'.A11YC_PAGES_URL.'&amp;undel=1&amp;url='.urlencode($url).'">Undelete</a></td>';
				}
				else
				{
					$html.= '<td><a href="'.A11YC_PAGES_URL.'&amp;del=1&amp;url='.urlencode($url).'">Delete</a></td>';
				}
				$html.= '</tr>';
			}
			$html.= '</table>';
		}
		else
		{
			$html.= '<p>'.A11YC_LANG_PAGES_NOT_FOUND.'</p>';
		}

		return array('', $html);
	}
}
