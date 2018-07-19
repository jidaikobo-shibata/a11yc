<?php
/**
 * A11yc\Validate\CssTotal
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

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
		static::$logs[$url]['css_suspicious_paren_num'][self::$unspec] = 5;
		static::$logs[$url]['css_suspicious_props'][self::$unspec] = 5;
		static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 5;
		static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 5;
		if ( ! static::$do_css_check) return;
		static::$logs[$url]['css_suspicious_paren_num'][self::$unspec] = 1;
		static::$logs[$url]['css_suspicious_props'][self::$unspec] = 1;
		static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 1;
		static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 1;

		$csses = static::css($url);
		if ( ! $csses)
		{
			static::$logs[$url]['css_suspicious_paren_num'][self::$unspec] = 4;
			static::$logs[$url]['css_suspicious_props'][self::$unspec] = 4;
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 4;
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 4;
			return;
		}

		if (Model\Css::$is_suspicious_paren_num)
		{
			static::$logs[$url]['css_suspicious_paren_num'][self::$unspec] = -1;
			static::$error_ids[$url]['css_suspicious_paren_num'][0]['id'] = '';
			static::$error_ids[$url]['css_suspicious_paren_num'][0]['str'] = '';
		}
		else
		{
			static::$logs[$url]['css_suspicious_paren_num'][self::$unspec] = 2;
		}

		foreach (Model\Css::$suspicious_props as $k => $prop)
		{
			static::$logs[$url]['css_suspicious_props'][self::$unspec] = -1;
			static::$error_ids[$url]['css_suspicious_props'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_props'][$k]['str'] = $prop;
		}
		if (static::$logs[$url]['css_suspicious_props'][self::$unspec] != -1)
		{
			static::$logs[$url]['css_suspicious_props'][self::$unspec] = 2;
		}

		foreach (Model\Css::$suspicious_prop_and_vals as $k => $prop)
		{
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = -1;
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['str'] = $prop;
		}
		if (static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] != -1)
		{
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 2;
		}

		foreach (Model\Css::$suspicious_val_prop as $k => $prop)
		{
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = -1;
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['str'] = join(':', $prop);
		}
		if (static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] != -1)
		{
			static::$logs[$url]['css_suspicious_prop_and_vals'][self::$unspec] = 2;
		}

	}
}
