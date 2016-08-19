<?php
/**
 * A11yc\Util
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Util
{
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
	 * number to 'A'
	 *
	 * @return  string
	 */
	public static function num2str($num, $default = '-')
	{
		$num = intval($num);
		return $num ? str_repeat('A', $num) : $default ;
	}

	/**
	 * replace '-' to '.' to convert '1-1-1' to '1.1.1'
	 *
	 * @return  string
	 */
	public static function key2code($str)
	{
		return str_replace('-', '.', $str);
	}

	/**
	 * sanitiz html
	 *
	 * @return  string | array
	 */
	public static function s($str)
	{
		if (is_array($str)) return array_map(array('\A11yc\Util', 's'), $str);
		return htmlspecialchars($str, ENT_QUOTES);
	}
}
