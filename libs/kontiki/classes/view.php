<?php
/**
 * Kontiki\View
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
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
	 * @param   string    path
	 * @return  void
	 */
	public static function forge($tpl_path)
	{
		if ( ! file_exists($tpl_path)) die('template path not found: '. s($tpl_path));
		static::$tpl_paths[] = rtrim($tpl_path, '/');
	}

	/**
	 * add template path
	 *
	 * @param   string    path
	 * @return  void
	 */
	public static function add_tpl_path($tpl_path)
	{
		if ( ! file_exists($tpl_path)) die('template path not found: '. s($tpl_path));
		array_unshift(static::$tpl_paths, rtrim($tpl_path, '/'));
	}

	/**
	 * tpl_path
	 * specified / fallback template path
	 *
	 * @return  string
	 */
	public static function tpl_path($tpl)
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
	 * @return  string
	 */
	public static function fetch($k)
	{
		return isset(static::$vals[$k]) ? static::$vals[$k] : FALSE;
	}

	/**
	 * fetch_tpl
	 * fetch specified / fallback template
	 *
	 * @return  string
	 */
	public static function fetch_tpl($tpl)
	{
		$tpl_path = static::tpl_path($tpl);
		if ( ! $tpl_path) die('template not found: '. s($tpl));

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
	 * @return  void
	 */
	public static function assign($key, $val, $escape = TRUE)
	{
		static::$vals[$key] = $escape ? Util::s($val) : $val;
	}

	/**
	 * display
	 *
	 * @param  array $tpls order of templates
	 * @return  void
	 */
	public static function display(array $tpls)
	{
		// extract
		extract (static::$vals);

		// render
		foreach ($tpls as $tpl)
		{
			echo static::fetch_tpl($tpl);
		}
	}
}
