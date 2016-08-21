<?php
/**
 * Kontiki\View
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class View
{
	public static $vals = array();

	/**
	 * tpl_path
	 * specified / fallback template path
	 *
	 * @return  string
	 */
	public static function tpl_path($tpl)
	{
		$path = KONTIKI_VIEWS_PATH.'/'.$tpl;
		$fallback = dirname(__DIR__).'/templates/'.$tpl;

		if (file_exists($path))
		{
			return $path;
		}
		elseif(file_exists($fallback))
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
		if ( ! $tpl_path) die('template not found');

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
	 * @return  void
	 */
	public static function display()
	{
		// extract
		extract (static::$vals);

		// render
		echo static::fetch_tpl('header.php');
		echo static::fetch_tpl('body.php');
		echo static::fetch_tpl('footer.php');
	}
}
