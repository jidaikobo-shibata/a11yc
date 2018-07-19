<?php
/**
 * A11yc\Validate\AreaHasAlt
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class AreaHasAlt extends Validate
{
	/**
	 * area has alt
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		static::$logs[$url]['area_has_alt'][self::$unspec] = 1;
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;
		static::$logs[$url]['area_has_alt'][self::$unspec] = 0;
		$is_exists = false;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$is_exists = true;

			$attrs = Element::getAttributes($m);
			$tstr = $ms[0][$k];
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$logs[$url]['area_has_alt'][$tstr] = 0;
				static::$error_ids[$url]['area_has_alt'][$k]['id'] = $tstr;
				static::$error_ids[$url]['area_has_alt'][$k]['str'] = @basename(@$attrs['href']);
			}
			else
			{
				static::$logs[$url]['area_has_alt'][$tstr] = 2;
			}
		}

		if ( ! $is_exists)
		{
			static::$logs[$url]['area_has_alt'][self::$unspec] = 4;
		}

		static::addErrorToHtml($url, 'area_has_alt', static::$error_ids[$url], 'ignores');
	}
}
