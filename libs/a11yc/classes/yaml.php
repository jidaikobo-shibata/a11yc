<?php
/**
 * A11yc\Yaml
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Yaml
{
	/**
	 * parse YAML message
	 *
	 * @return  array
	 */
	public static function fetch()
	{
		if ( ! class_exists('Spyc')) die('Spyc is not found');
		static $ret = '';
		if ($ret) return $ret;

		$levels     = file_get_contents(A11YC_RESOURCE_PATH.'/levels.yml');
		$principles = file_get_contents(A11YC_RESOURCE_PATH.'/principles.yml');
		$guidelines = file_get_contents(A11YC_RESOURCE_PATH.'/guidelines.yml');
		$criterions = file_get_contents(A11YC_RESOURCE_PATH.'/criterions.yml');
		$errors     = file_get_contents(A11YC_RESOURCE_PATH.'/errors.yml');
		$checks     = file_get_contents(A11YC_RESOURCE_PATH.'/checks.yml');
		$ret = \Spyc::YAMLLoadString($levels.$principles.$guidelines.$criterions.$errors.$checks);
		return $ret;
	}

	/**
	 * get each
	 *
	 * @return  array
	 */
	public static function each($file)
	{
		if ( ! class_exists('Spyc')) die('Spyc is not found');
		$file = basename($file);

		static $rets = array();
		if (isset($rets[$file])) return $rets[$file];

		$rets[$file] = \Spyc::YAMLLoadString(file_get_contents(A11YC_RESOURCE_PATH.'/'.$file.'.yml'));

		return $rets[$file];
	}
}
