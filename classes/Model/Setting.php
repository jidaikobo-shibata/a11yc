<?php
/**
 * A11yc\Model\Setting
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Setting
{
	protected static $vals = null;
	public static $json_encodes = array(
		'additional_criterions',
		'non_exist_and_passed_criterions',
		'non_use_techs',
		'bulk_checks',
		'bulk_results',
	);

	/**
	 * fetch setup
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		$vals = Data::fetch('setting', 'common', array(), $force);
		static::$vals = self::mustBeArray($vals);
		return static::$vals;
	}

	/**
	 * fetch setup
	 *
	 * @param String $field
	 * @param String $default
	 * @param String $force
	 * @return String|Array
	 */
	public static function fetch($field, $default = '', $force = false)
	{
		$settings = self::fetchAll($force);
		return Arr::get($settings, $field, $default);
	}

	/**
	 * update field
	 *
	 * @param Array $vals
	 * @return Array
	 */
	private static function mustBeArray($vals)
	{
		if (empty($vals)) return array();
		foreach (static::$json_encodes as $json_encode)
		{
			if ( ! isset($vals[$json_encode]) || empty($vals[$json_encode]))
			{
				$vals[$json_encode] = array();
			}
		}
		return $vals;
	}

	/**
	 * insert data
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	private static function insertData($vals)
	{
		Data::delete('setting', 'common');
		$vals = self::mustBeArray($vals);
		return Data::insert('setting', 'common', $vals);
	}

	/**
	 * update
	 *
	 * @param String $key
	 * @param Mixed  $value
	 * @return Bool
	 */
	public static function update($key, $value)
	{
		$settings = self::fetchAll(true);
		$settings[$key] = $value;
		return self::insertData($settings);
	}

	/**
	 * insert
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insert($vals)
	{
		$result = false;
		foreach ($vals as $key => $value)
		{
			$result = self::update($key, $value);
		}
		return $result;
	}

	/**
	 * update partial
	 *
	 * @param String $key
	 * @param String $inner_key
	 * @param Mixed  $inner_val
	 * @return Bool
	 */
	public static function updatePartial($key, $inner_key, $inner_val)
	{
		$vals = self::fetchAll(true);
		$default = in_array($key, static::$json_encodes) ? array() : '';
		$target = Arr::get($vals, $key, $default);
		$target[$inner_key] = $inner_val;
		return static::update($key, $target);
	}

	/**
	 * delete
	 *
	 * @param String $key
	 * @return Bool
	 */
	public static function delete($key)
	{
		$vals = self::fetchAll(true);
		unset($vals[$key]);
		return self::insertData($vals);
	}
}
