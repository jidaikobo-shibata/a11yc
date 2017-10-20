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
	/**
	 * parse YAML message
	 *
	 * @return Array
	 */
	public static function fetch()
	{
		// load Spyc - YAML lib.
		if ( ! class_exists('Spyc'))
		{
			include A11YC_LIB_PATH.'/spyc/Spyc.php';
		}

		if ( ! class_exists('Spyc')) Util::error('Spyc is not found');
		static $ret = '';
		if ($ret) return $ret;

		$levels     = file_get_contents(A11YC_RESOURCE_PATH.'/levels.yml');
		$principles = file_get_contents(A11YC_RESOURCE_PATH.'/principles.yml');
		$guidelines = file_get_contents(A11YC_RESOURCE_PATH.'/guidelines.yml');
		$criterions = file_get_contents(A11YC_RESOURCE_PATH.'/criterions.yml');
		$errors     = file_get_contents(A11YC_RESOURCE_PATH.'/errors.yml');
		$checks     = file_get_contents(A11YC_RESOURCE_PATH.'/checks.yml');
		$ret = \Spyc::YAMLLoadString($levels.$principles.$guidelines.$criterions.$errors.$checks);

		// codes, passes, conditions and non_exists
		// codes: array('1-1-1a' => '1-1-1', ... '2-1-1a' => '2-1-1', )
		// passes: array('1-1-1a' => array('1-1-1a', '1-1-1b', ...))
		// conditions: array('1-1-1' => array('1-1-1a', '1-1-1b',... '1-2-1a'...))
		// non_exists: array('1-1-1a' => '1-1-1', ...)
		$ret['codes'] = array();
		$ret['passed'] = array();
		$ret['conditions'] = array();
		foreach ($ret['checks'] as $v)
		{
			foreach ($v as $code => $vv)
			{
				// codes
				$ret['codes'][$code] = $vv['criterion']['code'];

				// passes and conditions
				if (isset($vv['pass']))
				{
					foreach ($vv['pass'] as $criterion => $vvv)
					{
						// passes
						$ret['passes'][$code] = Arr::get($ret, "passes.{$code}", array());
						$ret['passes'][$code] = array_merge($ret['passes'][$code], $vvv);

						// conditions
						$ret['conditions'][$criterion] = Arr::get($ret, "conditions.{$criterion}", array());
						$ret['conditions'][$criterion] = array_merge($ret['conditions'][$criterion], $vvv);
					}
				}

				// non exist
				if ( ! isset($vv['non-exist'])) continue;
				$ret['non_exists'][$code] = $vv['non-exist'];
			}
		}

		return $ret;
	}

	/**
	 * get each
	 *
	 * @param  String $file
	 * @return String
	 */
	public static function each($file)
	{
		if ( ! class_exists('Spyc')) Util::error('Spyc is not found');
		$file = basename($file);

		static $rets = array();
		if (isset($rets[$file])) return $rets[$file];

		$rets[$file] = \Spyc::YAMLLoadString(file_get_contents(A11YC_RESOURCE_PATH.'/'.$file.'.yml'));

		return $rets[$file];
	}
}
