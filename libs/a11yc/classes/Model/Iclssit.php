<?php
/**
 * A11yc\Model\Iclssit
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Iclssit
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

		$sql = 'SELECT * FROM '.A11YC_TABLE_ICLSSIT.Db::versionSql(false).';';
		$vals = Db::fetchAll($sql);
		static::$vals = Util::modCriterionBasedArr($vals);
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
	 * @return Integer|Bool
	 */
	public static function insert($vals)
	{
		$sql = 'INSERT INTO '.A11YC_TABLE_ICLSSIT.' (';
		$sql.= '`criterion`,';
		$sql.= '`value`,';
		$sql.= '`version`';
		$sql.= ') VALUES ';
		$sql.= '(?, ?, 0);';

		$r = Db::execute(
			$sql,
			array(
				Arr::get($vals, 'criterion', ''),
				Arr::get($vals, 'value', ''),
			)
		);

		if ( ! $r) return false;

		$sql = 'SELECT `id` FROM '.A11YC_TABLE_ICLSSIT;
		$sql.= ' ORDER BY `id` desc LIMIT 1;';
		$situation_id = Db::fetch($sql);

		return isset($situation_id['id']) ? intval($situation_id['id']) : false;
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
			$sql = 'INSERT INTO '.A11YC_TABLE_ICLSSIT.' (`key`, `value`, `version`) ';
			$sql.= ' VALUES (?, ?, 0);';
			$r = Db::execute($sql, array($key, $value));
		}
		else
		{
			$sql = 'UPDATE '.A11YC_TABLE_ICLSSIT.' SET `value` = ?';
			$sql.= ' WHERE `key` = ? AND `version` = 0;';
			$r = Db::execute($sql, array($value, $key));
		}

		return $r;
	}

}
