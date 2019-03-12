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
	public static $fields = array(
		'target_level'                    => 0,
		'selected_method'                 => 0,
		'stop_guzzle'                     => false,
		'standard'                        => 0,
		'show_results'                    => false,
		'hide_url_results'                => false,
		'hide_date_results'               => false,
		'hide_memo_results'               => false,
		'hide_failure_results'            => false,
		'client_name'                     => '',
		'declare_date'                    => '',
		'test_period'                     => '',
		'dependencies'                    => '',
		'policy'                          => '',
		'report'                          => '',
		'contact'                         => '',
		'base_url'                        => '',
		'basic_user'                      => '',
		'basic_pass'                      => '',
		'additional_criterions'           => array(),
		'non_exist_and_passed_criterions' => array(),
		'non_use_techs'                   => array(),
		'bulk_checks'                     => array(),
		'bulk_results'                    => array(),
	);

	/**
	 * fetch
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchRaw($force = false)
	{
		return Data::fetchArr('setting', 'common', array(), $force);
	}

	/**
	 * fetch setup
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		static::$vals = Data::filter(static::fetchRaw($force), static::$fields);
		return static::$vals;
	}

	/**
	 * fetch setup
	 *
	 * @param String $field
	 * @param String|Array $default
	 * @param Bool $force
	 * @return String|Array
	 */
	public static function fetch($field, $default = '', $force = false)
	{
		$settings = self::fetchAll($force);
		return Arr::get($settings, $field, $default);
	}

	/**
	 * fetch setup by Array
	 *
	 * @param String $field
	 * @param String|Array $default
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchArr($field, $default = array(), $force = false)
	{
		$settings = self::fetch($field, $default, $force);
		return is_array($settings) ? $settings : array() ;
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
	 * update all
	 *
	 * @param Array $vals
	 * @return Bool
	 */
	public static function updateAll($vals)
	{
		$vals = Data::filter($vals, self::fetchAll(true));
		if (static::fetchRaw(true))
		{
			return Data::update('setting', 'common', $vals);
		}
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
		return self::updateAll($settings);
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
		return self::updateAll($vals);
	}
}
