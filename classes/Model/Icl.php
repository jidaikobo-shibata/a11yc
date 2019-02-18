<?php
/**
 * A11yc\Model\Icl
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Icl
{
	protected static $vals = null;
	protected static $tree = null;
	public static $fields = array(
		'iclsit' => array(
			'title'      => '',
			'is_sit'     => true,
			'criterion'  => '',
			'seq'        => 0,
			'trash'      => 0,
		),
		'icl' => array(
			'title'       => '',
			'title_short' => '',
			'is_sit'      => false,
			'situation'   => '',
			'criterion'   => '',
			'identifier'  => '',
			'inspection'  => '',
			'techs'       => array(),
			'seq'         => 0,
			'trash'       => 0,
		)
	);

	/**
	 * fetch all
	 * depend on array order system
	 *
	 * @param Bool $force
	 * @return Array
	 */
	/*
		Data::deleteByKey('icl');
		Data::deleteByKey('iclsit');
		Setting::update('is_waic_imported', false);
	*/
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;

		$vals = array();
		foreach (Data::fetchRaw(0, Data::groupId()) as $v)
		{
			if ( ! in_array($v['key'], array('icl', 'iclsit'))) continue;
			$value = json_decode($v['value'], true);
			$type = Arr::get($value, 'is_sit', false) == true ? 'iclsit' : 'icl';
			$value = Data::filter($value, static::$fields[$type]);
			$value['id'] = $v['url'];
			$value['dbid'] = $v['id'];
			$vals[] = $value;
		}
		$vals = Util::multisort($vals); // don't use ksort. seq is better.
		static::$vals = Util::keyByColumn($vals);
		return static::$vals;
	}

	/**
	 * fetch tree
	 * depend on array order system
	 *
	 * @param Bool $using_only
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchTree($using_only = false, $force = false)
	{
		if ( ! is_null(static::$tree) && ! $force) return static::$tree;
		$vals = array();
		$usings = Setting::fetch('icl', array(), true);

		foreach (static::fetchAll(true) as $id => $v)
		{
			if ($v['is_sit']) continue;
			if ($using_only && ! in_array($id, $usings)) continue;
			$key = empty($v['situation']) ? 'none' : intval($v['situation']) ;
			$vals[$v['criterion']][$key][] = $id;
		}

		static::$tree = $vals;
		return static::$tree;
	}

	/**
	 * fetch
	 *
	 * @param Integer $id
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($id, $force = false)
	{
		return Arr::get(static::fetchAll($force), $id, array());
	}

	/**
	 * insert
	 *
	 * @param Array $vals
	 * @param Bool $is_sit
	 * @return Integer|Bool
	 */
	public static function insert($vals, $is_sit = false)
	{
		$type = $is_sit ? 'iclsit' : 'icl';
		$vals = Data::filter($vals, static::$fields[$type]);

		// use `url` as a id
		$sql = 'SELECT `url` FROM '.A11YC_TABLE_DATA;
		$sql.= ' WHERE `key` in ("iclsit", "icl") AND `group_id` = ? AND `version` = ?';
		$sql.= ' ORDER BY `id` desc LIMIT 1;';
		$data = Db::fetch($sql, array(Data::groupId(), Version::current()));
		$id = $data === false ? 0 : intval($data['url']) ;
		$id++;
		$vals['seq'] = $vals['seq'] != 0 ? $vals['seq'] : $id * 10;

		return Data::insert($type, $id, $vals) ? $id : false;
	}

	/**
	 * dbid
	 *
	 * @param Integer $id
	 * @return Integer|Bool
	 */
	private static function dbid($id)
	{
		$vals = static::fetch($id);
		return isset($vals['dbid']) ? intval($vals['dbid']) : false;
	}

	/**
	 * update
	 *
	 * @param Integer $id
	 * @param Mixed $vals
	 * @return Bool
	 */
	public static function update($id, $vals)
	{
		$vals = Data::filter($vals, static::fetch($id, true));
		return Data::updateById(self::dbid($id), $vals);
	}

	/**
	 * update partial
	 *
	 * @param Integer $id
	 * @param String $key
	 * @param Mixed $value
	 * @return Bool
	 */
	public static function updatePartial($id, $key, $value)
	{
		$id = self::dbid($id);
		$vals = static::fetch($id, true);
		$vals[$key] = $value;
		return Data::updateById($id, $vals);
	}

	/**
	 * purge
	 *
	 * @param Integer $id
	 * @return Bool
	 */
	public static function purge($id)
	{
		return Data::deleteById(self::dbid($id));
	}
}
