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
class Bulk extends Checklist
{
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
			$cs = Db::escapeStr($_POST['chk']);

			// delete all
			$sql = 'DELETE FROM '.A11YC_TABLE_BULK.';';
			Db::execute($sql);

			// insert
			foreach ($cs as $code => $v)
			{
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_BULK.' (`code`, `uid`, `memo`) VALUES ';
				$sql.= '('.Db::escapeStr($code).', '.$v['uid'].', '.$v['memo'].');';
				Db::execute($sql);
			}

			if ($_POST['update_all'] == 1) return;

			// update all
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.';';
			foreach (Db::fetch_all($sql) as $v)
			{
				$esc_url = Db::escapeStr($v['url']);
				foreach ($cs as $code => $vv)
				{
					$code = Db::escapeStr($code);
					$code_sql = 'SELECT code FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.$esc_url.' and `code` = '.$code.';';

					if ( ! Db::fetch($code_sql) && isset($vv['on']))
					{
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`) VALUES ('.$esc_url.', '.$code.', '.$vv['uid'].', '.$vv['memo'].');';
						Db::execute($sql);
					}

					// uncheck
					if ($_POST['update_all'] == 3 && Db::fetch($code_sql) && ! isset($vv['on']))
					{
						$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.$esc_url.' and `code` = '.$code.';';
						Db::execute($sql);
					}
				}

				// leveling
				list($results, $checked, $passed_flat) = Evaluate::evaluate_url($v['url']);
				$result = Evaluate::check_result($passed_flat);

				$update_done = intval($_POST['update_done']);
				$date = Db::escapeStr(date('Y-m-d'));

				// update/create page
				// do not update standard of each page
				if ($update_done == 1)
				{
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = '.$date.', `level` = '.$result.';';
					$sql.= ' WHERE `url` = '.$esc_url.';';
				}
				else
				{
					$done = $update_done == 2 ? 1 : 0 ;
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = '.$date.', `level` = '.$result.', `done` = '.$done;
					$sql.= ' WHERE `url` = '.$esc_url.';';
				}
				Db::execute($sql);
			}
		}
	}
}
