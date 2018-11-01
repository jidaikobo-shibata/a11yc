<?php
/**
 * A11yc\Model\Bulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Bulk
{
	/**
	 * fetch
	 *
	 * @return Array
	 */
	public static function fetchChecks()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_BCHECKS.';';
		$cs = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$cs[$v['code']]['is_checked'] = $v['is_checked'];
		}
		return $cs;
	}

	/**
	 * fetch
	 *
	 * @return Array
	 */
	public static function fetchResults()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_BRESULTS.';';
		$cs = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$cs[$v['criterion']]['memo'] = Arr::get($v, 'memo');
			$cs[$v['criterion']]['uid'] = $v['uid'];
			$cs[$v['criterion']]['result'] = Arr::get($v, 'result');
			$cs[$v['criterion']]['method'] = Arr::get($v, 'method');
		}
		return $cs;
	}

	/**
	 * dbio update default only
	 *
	 * @return Void
	 */
	public static function setDefault()
	{
		// results
		$sql = 'DELETE FROM '.A11YC_TABLE_BRESULTS.';';
		Db::execute($sql);

		foreach (Input::postArr('results') as $criterion => $v)
		{
			$memo   = stripslashes($v['memo']);
			$uid    = intval(Arr::get($v, 'uid', 0));
			$result = intval(Arr::get($v, 'result', 0));
			$method = intval(Arr::get($v, 'method', 0));

			$sql = 'INSERT INTO '.A11YC_TABLE_BRESULTS.' (`criterion`, `memo`, `uid`, `result`, `method`)';
			$sql.= ' VALUES (?, ?, ?, ?, ?);';
			Db::execute($sql, array($criterion, $memo, $uid, $result, $method));
		}

		// delete all
		$sql = 'DELETE FROM '.A11YC_TABLE_BCHECKS.';';
		Db::execute($sql);

		// insert
		$r = false;
		$chks = Input::postArr('chk');
		foreach ($chks as $code => $check)
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_BCHECKS.' (`code`, `is_checked`)';
			$sql.= ' VALUES (?, ?);';
			$r = Db::execute($sql, array($code, $check));
		}

		if ($r || empty($chks))
		{
			Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			return;
		}

		Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
	}

	/**
	 * dbio udpate all
	 *
	 * @return Void
	 */
	public static function all()
	{
		// update all except for in trash item
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0'.Db::currentVersionSql();

		foreach (Db::fetchAll($sql) as $v)
		{
			$url = $v['url'];

			// results
			self::updateResults($url);

			// uncheck
			self::updateUnchecks($url);

			// checks
			self::updateChecks($url);

			// update each page
			$update_done = intval(Input::post('update_done'));
			$date = date('Y-m-d');

			// do not update done flag
			if ($update_done == 1)
			{
				Pages::updateField($url, 'date', $date);
			}
			else
			{
				// update done flag done or not done
				$done = $update_done == 2 ? 1 : 0 ;
				Pages::updateField($url, 'date', $date);
				Pages::updateField($url, 'done', $done);
			}

			// update level
			Pages::updateField($url, 'level', Evaluate::getLevelByUrl($url));
		}
	}

	/**
	 * udpate results
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateResults($url)
	{
		foreach (Input::postArr('results') as $criterion => $vv)
		{
			$memo   = stripslashes($vv['memo']);
			$uid    = intval(Arr::get($vv, 'uid', 0));
			$result = intval(Arr::get($vv, 'result', 0));
			$method = intval(Arr::get($vv, 'method', 0));

			// add results
			$sql = 'SELECT * FROM '.A11YC_TABLE_RESULTS.' WHERE';
			$sql.= ' `url` = ? and `criterion` = ?'.Db::currentVersionSql().';';

			if ( ! Db::fetch($sql, array($url, $criterion)))
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_RESULTS;
				$sql.= ' (`url`, `criterion`, `uid`, `memo`, `result`, `method`, `version`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, ?, 0);';
				Db::execute($sql, array($url, $criterion, $uid, $memo, $result, $method));
			}

			// force update all
			if (Input::post('update_all') == 3)
			{
				$sql = 'UPDATE '.A11YC_TABLE_RESULTS;
				$sql.= ' SET `memo` = ?, `uid` = ?, `result` = ?, `method` = ?';
				$sql.= ' WHERE `criterion` = ? AND `url` = ? AND `version` = 0;';
				Db::execute($sql, array($memo, $uid, $result, $method, $criterion, $url));
			}
		}
	}

	/**
	 * udpate unchecks
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateUnchecks($url)
	{
		if (Input::post('update_all') == 3)
		{
			$sql = 'UPDATE '.A11YC_TABLE_CHECKS;
			$sql.= ' SET `is_checked` = ?';
			$sql.= ' WHERE `url` = ?'.Db::currentVersionSql().';';
			Db::execute($sql, array(false, $url));
		}
	}

	/**
	 * udpate checks
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateChecks($url)
	{
		$yml = Yaml::fetch();
		foreach (Input::postArr('chk') as $code => $vv)
		{
			// is_checked?
			$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE';
			$sql.= ' `url` = ? AND `code` = ?'.Db::currentVersionSql().';';
			$result = Db::fetch($sql, array($url, $code));

			$is_failure = ($yml['techs'][$code]['type'] == 'F');

			// add new code
			if ( ! $result)
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS;
				$sql.= ' (`url`, `code`, `is_checked`, `is_failure`, `version`)';
				$sql.= ' VALUES (?, ?, ?, ?, 0);';
				Db::execute($sql, array($url, $code, true, $is_failure));
			}
			else
			{
				$sql = 'UPDATE '.A11YC_TABLE_CHECKS.' SET ';
				$sql.= '`is_checked` = ?';
				$sql.= ' WHERE `url` = ? AND `code` = ?'.Db::currentVersionSql().';';
				Db::execute($sql, array(true, $url, $code));
			}
		}
	}
}
