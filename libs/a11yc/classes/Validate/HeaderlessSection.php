<?php
/**
 * A11yc\Validate\HeaderlessSection
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class HeaderlessSection extends Validate
{
	/**
	 * headerless section
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);

		preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section\>/is", $str, $secs);

		if ( ! $secs[0]) return;

		foreach ($secs[0] as $k => $v)
		{
			if ( ! preg_match("/\<h\d/", $v))
			{
				static::$error_ids[$url]['headerless_section'][$k]['id'] = $v;
				static::$error_ids[$url]['headerless_section'][$k]['str'] = $secs[1][$k];
			}
		}
		static::addErrorToHtml($url, 'headerless_section', static::$error_ids[$url], 'ignores');
	}
}
