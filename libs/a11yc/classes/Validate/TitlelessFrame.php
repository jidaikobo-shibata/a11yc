<?php
/**
 * A11yc\Validate\TitlelessFrame
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class TitlelessFrame extends Validate
{
	/**
	 * titleless frame
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') continue;
			$attrs = Element::getAttributes($v);

			if ( ! trim(Arr::get($attrs, 'title')))
			{
				static::$error_ids[$url]['titleless_frame'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['titleless_frame'][$k]['str'] = $ms[0][$k];
			}
		}
		static::addErrorToHtml($url, 'titleless_frame', static::$error_ids[$url], 'ignores');
	}
}
