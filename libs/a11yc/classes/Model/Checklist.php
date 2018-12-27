<?php
/**
 * A11yc\Model\Checklist
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Checklist
{
	protected static $checks = null;

	/**
	 * fetch checks from db
	 *
	 * @param  String $url
	 * @param Bool $force
	 * @return Bool|Array
	 */
	public static function fetch($url, $force = false)
	{
		if (empty($url)) return array();
		if (isset(static::$checks[$url]) && ! $force) return static::$checks[$url];
		$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?'.Db::versionSql().';';
		static::$checks[$url] = array();
		foreach (Db::fetchAll($sql, array($url)) as $v)
		{
			static::$checks[$url][$v['code']] = $v;
		}
		return static::$checks[$url];
	}

	/**
	 * fetch failures
	 *
	 * @param  String $url
	 * @return Bool|Array
	 */
	public static function fetchFailures($url = '')
	{
		$yml = Yaml::each('techs');
		$codes = array();
		foreach ($yml as $code => $v)
		{
			if ($v['type'] != 'F') continue;
			$codes[] = '"'.$code.'"';
		}

		$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE ';
		$sql.= ' `code` IN ('.join(', ', $codes).') AND `version` = 0';
		if ($url)
		{
			$sql.= ' AND `url` = ? ;';
			return Db::fetchAll($sql, array($url));
		}

		$rets = array();
		foreach (Db::fetchAll($sql) as $v)
		{
			$rets[$v['url']][] = $v;
		}
		return $rets;
	}

	/**
	 * insert results
	 *
	 * @param  Array  $vals
	 * @return Bool
	 */
	public static function insert($vals)
	{
		$url  = Arr::get($vals, 'url', '');
		$code = Arr::get($vals, 'code', '');
		if (empty($url) || empty($code)) return;

		$sql = 'SELECT `url` FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ? AND `code` = ?';
		$sql.= Db::currentVersionSql().';';
		if ( ! empty(Db::fetchAll($sql, array($url, $code)))) return false;

		$yml = Yaml::fetch();

		$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS;
		$sql.= ' (`url`, `code`, `is_checked`, `is_failure`, `version`)';
		$sql.= ' VALUES (?, ?, ?, ?, 0);';
		return Db::execute(
			$sql,
			array(
				$url,
				$code,
				TRUE,
				($yml['techs'][$code]['type'] == 'F')
			)
		);
	}

	/**
	 * update
	 *
	 * @param  String $url
	 * @param  Array  $checks
	 * @return Void
	 */
	public static function update($url, $checks)
	{
		$url = Util::urldec($url);

		// delete all from checks
		self::delete($url);

		$vals = array();
		foreach (array_keys($checks) as $code)
		{
			$vals = array(
				'code' => $code,
				'url' => $url,
			);

			static::insert($vals);
		}
	}

	/**
	 * delete
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function delete($url)
	{
		$url = Util::urldec($url);

		// delete all from checks
		$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?'.Db::currentVersionSql().';';
		return Db::execute($sql, array($url));
	}
}
