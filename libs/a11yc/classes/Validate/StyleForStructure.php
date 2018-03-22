<?php
/**
 * A11yc\Validate\StyleForStructure
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class StyleForStructure extends Validate
{
	/**
	 * style for structure
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;
		foreach ($ms[0] as $k => $m)
		{
			$attrs = Element::getAttributes($m);
			if ( ! array_key_exists('style', $attrs)) continue;
			if (
				strpos($attrs['style'], 'size') !== false ||
				strpos($attrs['style'], 'color') !== false // includes background-color
			)
			{
				static::$error_ids[$url]['style_for_structure'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['style_for_structure'][$k]['str'] = $m;
			}
		}
		static::addErrorToHtml($url, 'style_for_structure', static::$error_ids[$url], 'ignores');
	}
}
