<?php
/**
 * Kontiki\Lang
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;
class Lang
{
	/**
	 * get_langs
	 *
	 * @param  String $dir
	 * @return Array
	 */
	public static function get_langs($dir)
	{
		static $langs = array();
		if ($langs) return $langs;
		$dir = $dir ?: KONTIKI_PATH.'/lang';
		$langs = array_map('basename', glob($dir.'/*'));
		return $langs;
	}

	/**
	 * get_lang
	 *
	 * @return String
	 */
	public static function get_lang()
	{
	}
}
