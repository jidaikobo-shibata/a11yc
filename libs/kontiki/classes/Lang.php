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
	 * getLangs
	 *
	 * @param String $dir
	 * @return Array
	 */
	public static function getLangs($dir)
	{
		static $langs = array();
		if ( ! empty($langs)) return $langs;
		$dir = $dir ?: KONTIKI_PATH.'/lang';
		$langs = array_map('basename', glob($dir.'/*'));
		return $langs;
	}

	/**
	 * getLang
	 *
	 * @return String
	 */
	public static function getLang()
	{
	}
}
