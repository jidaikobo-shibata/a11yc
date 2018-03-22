<?php
/**
 * A11yc\Validate\ImgInputHasAlt
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class ImgInputHasAlt extends Validate
{
	/**
	 * is img input has alt
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach($ms[0] as $k => $m)
		{
			if (substr($m, 0, 6) !== '<input') continue;
			$attrs = Element::getAttributes($m);
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text at html5
			if (isset($attrs['type']) && $attrs['type'] != 'image') continue;

			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				static::$error_ids[$url]['img_input_has_alt'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['img_input_has_alt'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::addErrorToHtml($url, 'img_input_has_alt', static::$error_ids[$url], 'ignores');
	}
}
