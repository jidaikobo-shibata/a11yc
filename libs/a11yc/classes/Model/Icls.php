<?php
/**
 * A11yc\Model\Icls
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Icls
{
	protected static $vals = null;

	/**
	 * fetch all
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;

		$sql = 'SELECT * FROM '.A11YC_TABLE_ICLS.Db::versionSql(false).';';
		$vals = Db::fetchAll($sql);

		foreach ($vals as $k => $v)
		{
			$vals[$k]['implements'] = unserialize($v['implements']);
		}
		$vals = Util::modCriterionBasedArr($vals);
		static::$vals = $vals;
		return static::$vals;
	}

	/**
	 * fetch setup
	 *
	 * @param String $field
	 * @return String|Array
	 */
	public static function fetch($field, $default = '')
	{
		$settings = self::fetchAll();
		return Arr::get($settings, $field, $default);
	}

	/**
	 * insert
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insert($vals)
	{
		$sql = 'INSERT INTO '.A11YC_TABLE_ICLS.' (';
		$sql.= '`title`,';
		$sql.= '`inspection`,';
		$sql.= '`type`,';
		$sql.= '`implements`,';
		$sql.= '`criterion`,';
		$sql.= '`identifier`,';
		$sql.= '`criterions`,';
		$sql.= '`situation`,';
		$sql.= '`seq`,';
		$sql.= '`version`';
		$sql.= ') VALUES ';
		$sql.= '(?, ?, ?, ?, ?, ?, ?, ?, ?, 0);';

		$r = Db::execute(
			$sql,
			array(
				Arr::get($vals, 'title', ''),
				Arr::get($vals, 'inspection', ''),
				Arr::get($vals, 'type', ''),
				Arr::get($vals, 'implements', ''),
				Arr::get($vals, 'criterion', ''),
				Arr::get($vals, 'identifier', ''),
				Arr::get($vals, 'criterions', ''),
				Arr::get($vals, 'situation', ''),
				Arr::get($vals, 'seq', ''),
			)
		);

		return $r;
	}

	/**
	 * update field
	 *
	 * @param  String $key
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($key, $value)
	{
		$settings = self::fetchAll();
		if( ! isset($settings[$key]))
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_SITUATIONS.' (`key`, `value`, `version`) ';
			$sql.= ' VALUES (?, ?, 0);';
			$r = Db::execute($sql, array($key, $value));
		}
		else
		{
			$sql = 'UPDATE '.A11YC_TABLE_SITUATIONS.' SET `value` = ?';
			$sql.= ' WHERE `key` = ? AND `version` = 0;';
			$r = Db::execute($sql, array($value, $key));
		}

		return $r;
	}

}
