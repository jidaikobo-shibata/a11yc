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
	 * @param String $value
	 * @param String $url
	 * @param Array  $codes
	 * @param String $ua
	 * @param Bool   $force
	 * @param Mixed  $default
	 * @return Array|String
	 */
	private static function base($value, $url, $codes, $ua, $force, $default = array())
	{
		$codes = $codes ?: self::$codes;
		$name = static::codes2name($codes);
		if (isset(static::$results[$url][$name][$ua][$value]) && ! $force)
		{
			return static::$results[$url][$name][$ua][$value];
		}
		return $default;
	}

	/**
	 * errorCnts
	 *
	 * @param String $url
	 * @param Array  $codes
	 * @param String $ua
	 * @param Bool   $force
	 * @return Array
	 */
	public static function errorCnts($url, $codes = array(), $ua = 'using', $force = false)
	{
		return self::base('errs_cnts', $url, $codes, $ua, $force);
	}

	/**
	 * errors
	 *
	 * @param String $url
	 * @param Array  $codes
	 * @param String $ua
	 * @param Bool   $force
	 * @return Array
	 */
	public static function errors($url, $codes = array(), $ua = 'using', $force = false)
	{
		return self::base('errors', $url, $codes, $ua, $force);
	}

	/**
	 * get HighLightedHtml
	 *
	 * @param String $url
	 * @param Array  $codes
	 * @param String $ua
	 * @param Bool   $force
	 * @return String
	 */
	public static function highLightedHtml($url, $codes = array(), $ua = 'using', $force = false)
	{
		$retval = self::base('hl_html', $url, $codes, $ua, $force, '');
		if (is_array($retval)) return '';
		return $retval;
	}

	/**
	 * get error ids
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function errorIds($url)
	{
		return isset(static::$error_ids[$url]) ? static::$error_ids[$url] : array();
	}

	/**
	 * get logs
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function logs($url)
	{
		return isset(static::$logs[$url]) ? static::$logs[$url] : array();
	}
}
