<?php
/**
 * A11yc\Yaml
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Yaml
{
	protected static $data = array();
	protected static $non_interferences = array();

	/**
	 * parse YAML message
	 *
	 * @return Array
	 */
	public static function fetch()
	{
		if ( ! empty(static::$data)) return static::$data;

		// load Spyc - YAML lib.
		if ( ! class_exists('Spyc'))
		{
			include A11YC_LIB_PATH.'/spyc/Spyc.php';
		}

		if ( ! class_exists('Spyc')) Util::error('Spyc is not found');

		$standards   = file_get_contents(A11YC_RESOURCE_PATH.'/standards.yml');
		$levels      = file_get_contents(A11YC_RESOURCE_PATH.'/levels.yml');
		$principles  = file_get_contents(A11YC_RESOURCE_PATH.'/principles.yml');
		$guidelines  = file_get_contents(A11YC_RESOURCE_PATH.'/guidelines.yml');
		$criterions  = file_get_contents(A11YC_RESOURCE_PATH.'/criterions.yml');
		$errors      = file_get_contents(A11YC_RESOURCE_PATH.'/errors.yml');
		$techs       = file_get_contents(A11YC_RESOURCE_PATH.'/techs.yml');
		$techs_codes = file_get_contents(A11YC_RESOURCE_PATH.'/techs_codes.yml');
		$tests       = file_get_contents(A11YC_RESOURCE_PATH.'/tests.yml');
		$processes   = file_get_contents(A11YC_RESOURCE_PATH.'/processes.yml');
		static::$data = \Spyc::YAMLLoadString(
			$standards.
			$levels.
			$principles.
			$guidelines.
			$criterions.
			$errors.
			$techs.
			$techs_codes.
			$tests.
			$processes
		);
		return static::$data;
	}

	/**
	 * get each
	 *
	 * @param String $name
	 * @return Array
	 */
	public static function each($name)
	{
		$ret = self::fetch();
		return Arr::get($ret, $name, array());
	}

	/**
	 * Non-interferences
	 *
	 * @return Array
	 */
	public static function nonInterferences()
	{
		if ( ! empty(static::$non_interferences)) return static::$non_interferences;

		$yml = self::fetch();
		foreach ($yml['criterions'] as $criterion => $v)
		{
			if ( ! isset($v['non-interference'])) continue;
			static::$non_interferences[] = $criterion;
		}
		return static::$non_interferences;
	}
}
