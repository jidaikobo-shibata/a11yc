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

use A11yc\Element;

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
		static::$logs[$url]['headerless_section'][self::$unspec] = 1;
		$str = Element\Get::ignoredHtml($url);

		preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section\>/is", $str, $secs);

		if ( ! $secs[0])
		{
			static::$logs[$url]['headerless_section'][self::$unspec] = 4;
			return;
		}

		foreach ($secs[0] as $k => $v)
		{
			if ( ! preg_match("/\<h\d/", $v))
			{
				static::$logs[$url]['headerless_section'][$v] = -1;
				static::$error_ids[$url]['headerless_section'][$k]['id'] = Element\Get::firstTag($v);
				static::$error_ids[$url]['headerless_section'][$k]['str'] = Element\Get::firstTag($v);
			}
			else
			{
				static::$logs[$url]['headerless_section'][$v] = 2;
			}
		}
		static::addErrorToHtml($url, 'headerless_section', static::$error_ids[$url], 'ignores');
	}
}
