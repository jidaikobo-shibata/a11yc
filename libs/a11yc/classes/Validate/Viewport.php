<?php
/**
 * A11yc\Validate\Viewport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class Viewport extends Validate
{
	/**
	 * viewport
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if (Validate::$is_partial == true) return;

		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[1] as $k => $tag)
		{
			if ($tag == 'meta' && strpos($ms[2][$k], 'user-scalable=no') !== false)
			{
				static::$error_ids[$url]['user_scalable_no'][0]['id'] = $ms[0][$k];
				static::$error_ids[$url]['user_scalable_no'][0]['str'] = 'user-scalable=no';
			}
		}
		static::addErrorToHtml($url, 'user_scalable_no', static::$error_ids[$url]);
	}
}
