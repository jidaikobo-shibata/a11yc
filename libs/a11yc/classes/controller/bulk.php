<?php
/**
 * A11yc\Bulk
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Controller_Bulk extends Controller_Checklist
{
	/**
	 * action index
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		$setup = Controller_Setup::fetch_setup();
		if ( ! $setup['target_level'])
		{
			\A11yc\View::assign('errors', array(A11YC_LANG_ERROR_NON_TARGET_LEVEL));
		}
		static::checklist('bulk');
	}

	/**
	 * fetch_results
	 *
	 * @return  array
	 */
	public static function fetch_results()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_BULK.';';
		$cs = array();
		foreach (Db::fetch_all($sql) as $v)
		{
			$cs[$v['code']]['memo'] = $v['memo'];
			$cs[$v['code']]['uid'] = $v['uid'];
		}
		return $cs;
	}

	/**
	 * dbio
	 *
	 * @param   string     $url
	 * @return  void
	 */
	public static function dbio($url)
	{
		if ($_POST)
		{
			$cs = $_POST['chk'];

			// delete all
			$sql = 'DELETE FROM '.A11YC_TABLE_BULK.';';
			Db::execute($sql);

			// insert
			foreach ($cs as $code => $v)
			{
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_BULK.' (`code`, `uid`, `memo`) VALUES ';
				$sql.= '(?, ?, ?);';
				if (Db::execute($sql, array($code, $v['uid'], $v['memo'])))
				{
					\A11yc\View::assign('messages', array(A11YC_LANG_UPDATE_SUCCEED));
				}
				else
				{
					\A11yc\View::assign('errors', array(A11YC_LANG_UPDATE_FAILED));
				}
			}

			if ($_POST['update_all'] == 1) return;

			// update all
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.';';
			foreach (Db::fetch_all($sql) as $v)
			{
				foreach ($cs as $code => $vv)
				{
					$code_sql = 'SELECT code FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? and `code` = ?;';

					if ( ! Db::fetch($code_sql, array($v['url'], $code)) && isset($vv['on']))
					{
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`)';
						$sql.= ' VALUES (?, ?, ?, ?);';
						Db::execute($sql, array($v['url'], $code, $vv['uid'], $vv['memo']));
					}

					// uncheck
					if ($_POST['update_all'] == 3 && Db::fetch($code_sql, array($v['url'], $code)) && ! isset($vv['on']))
					{
						$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? and `code` = ?;';
						Db::execute($sql, array($v['url'], $code));
					}
				}

				// leveling
				list($results, $checked, $passed_flat) = Evaluate::evaluate_url($v['url']);
				$result = Evaluate::check_result($passed_flat);

				$update_done = intval($_POST['update_done']);
				$date = Db::escape(date('Y-m-d'));

				// update/create page
				// do not update standard of each page
				if ($update_done == 1)
				{
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = ?, `level` = ? WHERE `url` = ?;';
					Db::execute($sql, array($date, $result, $v['url']));
				}
				else
				{
					$done = $update_done == 2 ? 1 : 0 ;
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = ?, `level` = ?, `done` = ? WHERE `url` = ?;';
					Db::execute($sql, array($date, $result, $done, $v['url']));
				}
			}
		}
	}
}
