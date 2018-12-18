<?php
/**
 * A11yc\Model\Results
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Results
{
	protected static $results = null;

	/**
	 * fetch results from db
	 *
	 * @param  String $url
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($url, $force = false)
	{
		if (isset(static::$results[$url]) && ! $force) return static::$results[$url];
		$sql = 'SELECT * FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ?'.Db::versionSql().';';

		foreach (Db::fetchAll($sql, array($url)) as $v)
		{
			static::$results[$url][$v['criterion']] = $v;
		}
		if ( ! isset(static::$results[$url]))
		{
			static::$results[$url] = array();
		}

		return static::$results[$url];
	}

	/**
	 * fetch passed results from db
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function fetchPassed($url)
	{
		$results = self::fetch($url);
		if ( ! is_array($results)) return array();
		$retval = array();
		foreach ($results as $result)
		{
			if (intval($result['result']) === 0) continue;
			if (intval($result['result']) === -1) continue;
			$retval[] = $result['criterion'];
		}
		return $retval;
	}

	/**
	 * update results
	 *
	 * @param  String $url
	 * @param  array  $results
	 * @return Void
	 */
	public static function update($url, $results)
	{
		$url = Util::urldec($url);

		// delete all from results
		$sql = 'DELETE FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ?';
		$sql.= Db::currentVersionSql().';';
		Db::execute($sql, array($url));

		// update
		foreach ($results as $criterion => $v)
		{
			$v['criterion'] = $criterion;
			$v['url'] = $url;
			static::insert($v);
		}
	}

	/**
	 * insert results
	 *
	 * @param  Array  $vals
	 * @return Bool
	 */
	public static function insert($vals)
	{
		$url       = Arr::get($vals, 'url', '');
		$criterion = Arr::get($vals, 'criterion', '');
		if (empty($url) || empty($criterion)) return;

		$sql = 'SELECT `url` FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ? AND `criterion` = ?';
		$sql.= Db::currentVersionSql().';';
		if ( ! empty(Db::fetchAll($sql, array($url, $criterion)))) return false;

		$sql = 'INSERT INTO '.A11YC_TABLE_RESULTS;
		$sql.= ' (`url`, `criterion`, `memo`, `uid`, `result`, `method`, `version`)';
		$sql.= ' VALUES (?, ?, ?, ?, ?, ?, 0);';
		return Db::execute(
			$sql,
			array(
				$url,
				$criterion,
				stripslashes(Arr::get($vals, 'memo', '')),
				intval(Arr::get($vals, 'uid', 0)),
				intval(Arr::get($vals, 'result', 0)),
				intval(Arr::get($vals, 'method', 0))
			)
		);
	}

	/**
	 * pages of passed
	 *
	 * @param  String $target_level
	 * @return Array
	 */
	public static function passedPages($target_level)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
		$sql.= ' WHERE `level` >= '.$target_level;
		$sql.= ' AND `done` = 1 AND `trash` = 0';
		$sql.= Db::versionSql();
		return Db::fetchAll($sql);
	}

	/**
	 * pages of unpassed
	 *
	 * @param  String $target_level
	 * @return Array
	 */
	public static function unpassedPages($target_level)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES;
		$sql.= ' WHERE `level` < '.$target_level;
		$sql.= ' AND `done` = 1 AND `trash` = 0';
		$sql.= Db::versionSql();
		return Db::fetchAll($sql);
	}
}
