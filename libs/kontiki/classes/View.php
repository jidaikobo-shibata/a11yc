<?php
/**
 * Kontiki\View
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class View
{
	public static $vals = array();
	public static $tpl_paths;

	/**
	 * set template path
	 *
	 * @param String $tpl_path
	 * @return Void
	 */
	public static function forge($tpl_path)
	{
		if ( ! file_exists($tpl_path)) Util::error('template path not found: '. s($tpl_path));
		static::$tpl_paths[] = rtrim($tpl_path, '/');
	}

	/**
	 * add template path
	 * added template pathes is stronger than forged template path
	 *
	 * @param String $tpl_path
	 * @return Void
	 */
	public static function addTplPath($tpl_path)
	{
		if ( ! file_exists($tpl_path)) Util::error('template path not found: '. s($tpl_path));
		array_unshift(static::$tpl_paths, rtrim($tpl_path, '/'));
	}

	/**
	 * tpl_path
	 * specified / fallback template path
	 *
	 * @param String $tpl
	 * @return String|Bool
	 */
	public static function tplPath($tpl)
	{
		foreach (static::$tpl_paths as $tpl_path)
		{
			$path = $tpl_path.'/'.$tpl;

			if (file_exists($path))
			{
				return $path;
			}
		}

		$fallback = dirname(__DIR__).'/views/'.$tpl;
		if (file_exists($fallback))
		{
			return $fallback;
		}

		return false;
	}

	/**
	 * fetch
	 * fetch vals
	 *
	 * @return Integer|String $k
	 * @return String|Bool
	 */
	public static function fetch($k)
	{
		return isset(static::$vals[$k]) ? static::$vals[$k] : FALSE;
	}

	/**
	 * fetchTpl
	 * fetch specified / fallback template
	 *
	 * @param String $tpl
	 * @return String
	 */
	public static function fetchTpl($tpl)
	{
		$tpl_path = static::tplPath($tpl);
		if ($tpl_path === false) Util::error('template not found: '. Util::s($tpl));

		// extract
		extract (static::$vals);

		// out buffer
		ob_start();
		require($tpl_path);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	/**
	 * assign
	 *
	 * @param String $key
	 * @param String $val
	 * @param Bool $escape
	 * @return Void
	 */
	public static function assign($key, $val, $escape = TRUE)
	{
		if (is_numeric($val) || is_bool($val))
		{
			static::$vals[$key] = $val;
			return;
		}
		static::$vals[$key] = $escape ? Util::s($val) : $val;
	}

	/**
	 * display
	 *
	 * @param Array $tpls order of templates
	 * @return Void
	 */
	public static function display(array $tpls)
	{
		// extract
		extract (static::$vals);

		// render
		foreach ($tpls as $tpl)
		{
			echo static::fetchTpl($tpl);
		}
	}
}
