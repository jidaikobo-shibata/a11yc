<?php
/**
 * A11yc\Bulk
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
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
			Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
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
			if (Arr::get($v, 'memo') == '') continue;
			$cs[$v['code']]['memo'] = $v['memo'];
			$cs[$v['code']]['uid'] = $v['uid'];
		}
		return $cs;
	}

	/**
	 * fetch ng
	 *
	 * @return  array
	 */
	public static function fetch_ngs()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_BULK_NGS.';';
		$ngs = array();
		foreach (Db::fetch_all($sql) as $v)
		{
			$ngs[$v['criterion']]['memo'] = $v['memo'];
			$ngs[$v['criterion']]['uid'] = $v['uid'];
		}
		return $ngs;
	}

	/**
	 * dbio
	 *
	 * @param   string     $url
	 * @return  void
	 */
	public static function dbio($url)
	{
		if (Input::post())
		{
			$cs = Input::post('chk');

			// ngs
			$sql = 'DELETE FROM '.A11YC_TABLE_BULK_NGS.';';
			Db::execute($sql);

			foreach (Input::post('ngs') as $criterion => $v)
			{
				if ( ! trim($v['memo'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_BULK_NGS.' (`criterion`, `uid`, `memo`)';
				$sql.= ' VALUES (?, ?, ?);';
				Db::execute($sql, array($criterion, $v['uid'], $v['memo']));
			}

			// delete all
			$sql = 'DELETE FROM '.A11YC_TABLE_BULK.';';
			Db::execute($sql);

			// insert
			foreach ($cs as $code => $v)
			{
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_BULK.' (`code`, `uid`, `memo`) VALUES ';
				$sql.= '(?, ?, ?);';
				if (Db::execute($sql, array($code, (int) $v['uid'], $v['memo'])))
				{
					Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
				}
			}

			if (Input::post('update_all') == 1) return;

			// update all except for in trash item
			$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0;';
			foreach (Db::fetch_all($sql) as $v)
			{
				// ngs
				foreach (Input::post('ngs') as $criterion => $vv)
				{
					// add ngs
					$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS_NGS.' WHERE `url` = ? and `criterion` = ?;';
					if (
						! Db::fetch($sql, array($v['url'], $criterion)) &&
						Arr::get($vv, 'memo')
					)
					{
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS_NGS.' (`url`, `criterion`, `uid`, `memo`)';
						$sql.= ' VALUES (?, ?, ?, ?);';
						Db::execute($sql, array($v['url'], $criterion, $vv['uid'], $vv['memo']));
					}

					// force update ngs
					if (Input::post('update_all') == 3)
					{
						$sql = 'UPDATE '.A11YC_TABLE_CHECKS_NGS.' SET `memo` = ?, `uid` = ? WHERE `criterion` = ?;';
						Db::execute($sql, array($vv['memo'], $vv['uid'], $criterion));
					}
				}

				// checks and unchecks
				foreach ($cs as $code => $vv)
				{
					// add checks
					$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? and `code` = ?;';
					$result = Db::fetch($sql, array($v['url'], $code));

					// add new code
					if ( ! $result)
					{
						if ( ! isset($vv['on']) && empty($vv['memo'])) continue;
						$passed = isset($vv['on']);
						$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS;
						$sql.= ' (`url`, `code`, `uid`, `memo`, `passed`) VALUES (?, ?, ?, ?, ?)';
						Db::execute($sql, array($v['url'], $code, $vv['uid'], $vv['memo'], $passed));
					}

					// uncheck
					if (
						Input::post('update_all') == 3 &&
						$result['passed'] &&
						! isset($vv['on'])
					)
					{
						$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? and `code` = ?;';
						Db::execute($sql, array($v['url'], $code));
					}
				}

				// update each page
				$update_done = intval(Input::post('update_done'));
				$date = date('Y-m-d');

				// do not update done flag
				if ($update_done == 1)
				{
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = ? WHERE `url` = ?;';
					Db::execute($sql, array($date, $v['url']));
				}
				else
				{
					// update done flag done or not done
					$done = $update_done == 2 ? 1 : 0 ;
					$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
					$sql.= '`date` = ?, `done` = ? WHERE `url` = ?;';
					Db::execute($sql, array($date, $done, $v['url']));
				}

				// update level
				static::update_page_level($v['url']);
			}
		}
	}
}
