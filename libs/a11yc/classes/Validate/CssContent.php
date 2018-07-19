<?php
/**
 * A11yc\Validate\CssContent
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class CssContent extends Validate
{
	/**
	 * check content
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['css_is_meanfull_content'][self::$unspec] = 5;
		if ( ! static::$do_css_check) return;
		static::$logs[$url]['css_is_meanfull_content'][self::$unspec] = 1;

		$csses = static::css($url);
		if ( ! $csses)
		{
			static::$logs[$url]['css_is_meanfull_content'][self::$unspec] = 4;
			return;
		}

		$is_exists = false;
		$k = 0;
		foreach ($csses as $each_csses)
		{
			foreach ($each_csses as $selector => $props)
			{
				if ( ! isset($props['content'])) continue;
				$is_exists = true;
				if (
					in_array(
						$props['content'],
						array("''", '""', '.', 'none', '"."', "'.'", '" "', "' '")
					)
				)
				{
					continue; // clearfix or none
				}
				if (in_array(substr($props['content'], 0, 2), array("'\\", '"\\'))) continue; // decoration

				static::$logs[$url]['css_is_meanfull_content'][self::$unspec] = -1;
				static::$error_ids[$url]['css_is_meanfull_content'][$k]['id'] = '';
				static::$error_ids[$url]['css_is_meanfull_content'][$k]['str'] = $selector.': '.Util::s($props['content']);
				$k++;
			}
		}

		if ( ! $is_exists)
		{
			static::$logs[$url]['css_is_meanfull_content'][self::$unspec] = 4;
		}

	}
}
