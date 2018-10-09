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
use A11yc\Model;

class CssTotal extends Validate
{
	/**
	 * check content
	 *
	 * @param  String $url
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

		static::setLog($url, $error_names, self::$unspec, 5);
		if ( ! static::$do_css_check) return;
		static::setLog($url, $error_names, self::$unspec, 1);

		$csses = static::css($url);
		if ( ! $csses)
		{
			static::setLog($url, $error_names, self::$unspec, 4);
			return;
		}

		if (Model\Css::$is_suspicious_paren_num)
		{
			static::setError($url, 'css_suspicious_paren_num', 0, '', '');
		}
		else
		{
			static::setLog($url, 'css_suspicious_paren_num', self::$unspec, 2);
		}

		foreach (Model\Css::$suspicious_props as $k => $prop)
		{
			static::setError($url, 'css_suspicious_props', $k, '', $prop);
		}
		if (static::$logs[$url]['css_suspicious_props'][self::$unspec] != -1)
		{
			static::setLog($url, 'css_suspicious_props', self::$unspec, 2);
		}

		foreach (Model\Css::$suspicious_prop_and_vals as $k => $prop)
		{
			static::setError($url, 'css_suspicious_prop_and_vals', $k, '', $prop);
		}
		if (static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] != -1)
		{
			static::setLog($url, 'css_suspicious_prop_and_vals', self::$unspec, 2);
		}

		foreach (Model\Css::$suspicious_val_prop as $k => $prop)
		{
			static::setError($url, 'css_suspicious_prop_and_vals', $k, '', join(':', $prop));
		}
		if (static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] != -1)
		{
			static::setLog($url, 'css_suspicious_prop_and_vals', self::$unspec, 2);
		}

	}
}
