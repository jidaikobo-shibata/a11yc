<?php
/**
 * * A11yc\Validate\Check\HeaderlessSection
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

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
		static::setLog($url, 'headerless_section', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section\>/is", $str, $secs);

		if ( ! $secs[0])
		{
			static::setLog($url, 'headerless_section', self::$unspec, 4);
			return;
		}

		foreach ($secs[0] as $k => $v)
		{
			$tstr = Element\Get::firstTag($v);
			if ( ! preg_match("/\<h\d/", $v))
			{
				static::setError($url, 'headerless_section', $k, $tstr, $tstr);
			}
			else
			{
				static::setLog($url, 'headerless_section', $tstr, 2);
			}
		}
		static::addErrorToHtml($url, 'headerless_section', static::$error_ids[$url], 'ignores');
	}
}
