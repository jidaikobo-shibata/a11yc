<?php
/**
 * * A11yc\Validate\Check\CssTotal
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;
use A11yc\Validate;
use A11yc\Model;

class CssTotal extends Validate
{
	/**
	 * check content
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$error_names = array(
			'css_suspicious_paren_num',
			'css_suspicious_props',
			'css_suspicious_prop_and_vals',
			'css_suspicious_prop_and_vals',
		);

		Validate\Set::log($url, $error_names, self::$unspec, 5);
		if ( ! static::$do_css_check) return;
		Validate\Set::log($url, $error_names, self::$unspec, 1);

		$csses = static::css($url);
		if (empty($csses))
		{
			Validate\Set::log($url, $error_names, self::$unspec, 4);
			return;
		}

		Validate\Set::errorAndLog(
			Model\Css::$is_suspicious_paren_num,
			$url,
			'css_suspicious_paren_num',
			0,
			'',
			''
		);

		self::serErrorOrLog(
			Model\Css::$suspicious_props,
			$url,
			'css_suspicious_props',
			''
		);

		self::serErrorOrLog(
			Model\Css::$suspicious_prop_and_vals,
			$url,
			'css_suspicious_prop_and_vals',
			''
		);

		foreach (Model\Css::$suspicious_val_prop as $k => $prop)
		{
			Validate\Set::error($url, 'css_suspicious_prop_and_vals', $k, '', join(':', $prop));
		}
		if (static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] != -1)
		{
			Validate\Set::log($url, 'css_suspicious_prop_and_vals', self::$unspec, 2);
		}

	}

	/**
	 * set error or log
	 *
	 * @param Array $props
	 * @param String $url
	 * @param String|Array $error_name
	 * @param String $id
	 * @return Void
	 */
	private static function serErrorOrLog($props, $url, $error_name, $id)
	{
		foreach ($props as $count => $prop)
		{
			Validate\Set::error($url, $error_name, $count, $id, $prop);
		}
		if (static::$logs[$url][$error_name][self::$unspec] != -1)
		{
			Validate\Set::log($url, $error_name, self::$unspec, 2);
		}
	}
}
