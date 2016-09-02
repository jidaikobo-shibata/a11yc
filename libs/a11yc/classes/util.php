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
class Util extends \Kontiki\Util
{
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
	 * fetch html
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_html($url)
	{
		static $html = array();
		if (isset($html[$url])) return $html[$url];
		$html[$url] = strtolower(@file_get_contents($url));
		return $html[$url];
	}

	/**
	 * fetch page title
	 *
	 * @param   string     $url
	 * @return  string
	 */
	public static function fetch_page_title($url)
	{
		static $title = array();
		if (isset($title[$url])) return $title[$url];
		$html = static::fetch_html($url);
		preg_match("/<title.*?>(.+?)<\/title>/", $html, $m);
		$title[$url] = isset($m[1]) ? $m[1] : '';
		return $title[$url];
	}
}
