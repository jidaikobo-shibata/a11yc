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
		if ( ! static::doCssCheck()) return;

		$csses = static::css($url);
		if ( ! $csses) return;

		if (Model\Css::$is_suspicious_paren_num)
		{
			static::$error_ids[$url]['css_suspicious_paren_num'][0]['id'] = '';
			static::$error_ids[$url]['css_suspicious_paren_num'][0]['str'] = '';
		}

		foreach (Model\Css::$suspicious_props as $k => $prop)
		{
			static::$error_ids[$url]['css_suspicious_props'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_props'][$k]['str'] = $prop;
		}

		foreach (Model\Css::$suspicious_prop_and_vals as $k => $prop)
		{
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['str'] = $prop;
		}

		foreach (Model\Css::$suspicious_val_prop as $k => $prop)
		{
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['id'] = '';
			static::$error_ids[$url]['css_suspicious_prop_and_vals'][$k]['str'] = join(':', $prop);
		}
	}
}
