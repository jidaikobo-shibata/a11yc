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
	 * @param Bool $is_all
	 * @param Bool $force
	 * @return Array
	 */
	/*
		Data::deleteByKey('icl');
		Data::deleteByKey('iclsit');
		Setting::update('is_waic_imported', false);
	*/
	public static function fetchAll($is_all = false, $force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		$uses = Setting::fetch('icl');

		$vals = array();
		foreach (Data::fetchRaw(0, Data::groupId()) as $v)
		{
			if ( ! in_array($v['key'], array('icl', 'iclsit'))) continue;
			$value = json_decode($v['value'], true);
			$type = Arr::get($value, 'is_sit', false) == true ? 'iclsit' : 'icl';
			$value = Data::filter($value, static::$fields[$type]);
			$id = $v['url'];
			if ($is_all === false && ! in_array($id, $uses)) continue;
			$value['id'] = $id;
			$value['dbid'] = $v['id'];
			$vals[] = $value;
		}
		$vals = Util::multisort($vals); // don't use ksort. seq is better.
		static::$vals = Util::keyByColumn($vals);
		return static::$vals;
	}

	/**
	 * fetch tree
	 * depend on Array order system
	 *
	 * @param Bool $is_all
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchTree($is_all = false, $force = false)
	{
		if ( ! is_null(static::$tree) && ! $force) return static::$tree;
		$vals = array();

		foreach (static::fetchAll($is_all, true) as $id => $v)
		{
			if ($v['is_sit']) continue;
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
		return Arr::get(static::fetchAll(true, $force), $id, array());
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
		if ($dbid = self::dbid($id))
		{
			$vals = Data::filter($vals, static::fetch($id, true));
			return Data::updateById($dbid, $vals);
		}
		return false;
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
		if ($dbid = self::dbid($id))
		{
			$vals = static::fetch($dbid, true);
			$vals[$key] = $value;
			return Data::updateById($dbid, $vals);
		}
		return false;
	}

	/**
	 * purge
	 *
	 * @param Integer $id
	 * @return Bool
	 */
	public static function purge($id)
	{
		if ($dbid = self::dbid($id))
		{
			return Data::deleteById($dbid);
		}
		return false;
	}
}
