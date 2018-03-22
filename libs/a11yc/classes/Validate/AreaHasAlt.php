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
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$attrs = Element::getAttributes($m);
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids[$url]['area_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['area_has_alt'][$k]['str'] = @basename(@$attrs['href']);
			}
		}
		static::addErrorToHtml($url, 'area_has_alt', static::$error_ids[$url], 'ignores');
	}
}
