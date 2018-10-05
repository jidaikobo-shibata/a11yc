<?php
/**
 * A11yc\Validate\Get
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class Get extends Validate
{
	/**
	 * errorCnts
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return Array
	 */
	public static function errorCnts($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua]['errs_cnts']) && ! $force) return static::$results[$url][$name][$ua]['errs_cnts'];
		return array();
	}

	/**
	 * errors
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return Array
	 */
	public static function errors($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);

		if (isset(static::$results[$url][$name][$ua]['errors']) && ! $force) return static::$results[$url][$name][$ua]['errors'];
		return array();
	}

	/**
	 * get HighLightedHtml
	 *
	 * @param  String $url
	 * @param  Array  $codes
	 * @param  String $ua
	 * @param  Bool   $force
	 * @return String
	 */
	public static function highLightedHtml($url, $codes = array(), $ua = 'using', $force = false)
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);

		if (isset(static::$results[$url][$name][$ua]['hl_html']) && ! $force) return static::$results[$url][$name][$ua]['hl_html'];
		return '';
	}

	/**
	 * get error ids
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function errorIds($url)
	{
		return isset(static::$error_ids[$url]) ? static::$error_ids[$url] : array();
	}

	/**
	 * get logs
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function logs($url)
	{
		return isset(static::$logs[$url]) ? static::$logs[$url] : array();
	}
}
