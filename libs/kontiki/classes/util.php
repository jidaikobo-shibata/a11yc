<?php
/**
 * Kontiki\Util
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class Util
{
	/**
	 * add autoloader path
	 *
	 * @return  void
	 */
	public static function add_autoloader_path($path, $namespace = '')
	{
		spl_autoload_register(
			function ($class_name) use ($path, $namespace)
			{
				// check namespace
				$class = strtolower($class_name);
				$strlen = strlen($namespace);

				if (substr($class, 0, $strlen) !== $namespace) return;
				$class = substr($class, $strlen + 1);

				// underscores are directories
				$path.= str_replace('_', '/', $class);

				// require
				require $path.'.php';

				// init
				if (method_exists($class_name, '_init') and is_callable($class_name.'::_init'))
				{
					call_user_func($class_name.'::_init');
				}

			}
		);
	}

	/**
	 * get current uri
	 *
	 * @return  string
	 */
	public static function uri()
	{
		$uri = static::is_ssl() ? 'https' : 'http';
		$uri.= '://'.$_SERVER["HTTP_HOST"].rtrim($_SERVER["REQUEST_URI"], '/');
		return $uri;
	}

	/**
	 * add query strings
	 *
	 * @return  string
	 */
	public static function add_query_strings($uri, $query_strings = array())
	{
		$delimiter = strpos($uri, '?') !== false ? '&amp;' : '?';
		return $uri.$delimiter.join('&amp;', $query_strings);
	}

	/**
	 * is ssl
	 *
	 * @return  bool
	 */
	public static function is_ssl()
	{
		return isset($_SERVER['HTTP_X_SAKURA_FORWARDED_FOR']) ||
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']);
	}

	/**
	 * sanitiz html
	 *
	 * @return  string | array
	 */
	public static function s($str)
	{
		if (is_array($str)) return array_map(array('\Kontiki\Util', 's'), $str);
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * performance
	 *
	 * @return  void
	 */
	public static function performance($startTime, $startMemory)
	{
		$endTime = microtime(true);
		View::assign('convert_time', number_format($endTime - $startTime, 2 ).' sec.');
		$endMemory = memory_get_usage(false);
		$ussage = ($endMemory - $startMemory) / 1048576;
		View::assign('memory_get_usage', round($ussage, 2 ).' MB.');
	}
}
