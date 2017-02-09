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
	 * @return  array
	 */
	public static function get_langs()
	{
		static $langs = array();
		if ($langs) return $langs;
		$langs = array_map('basename', glob(KONTIKI_PATH.'lang/*'));
		return $langs;
	}

	/**
	 * get_lang
	 *
	 * @return  string
	 */
	public static function get_lang()
	{
	}
}
