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
use A11yc\Validate;

class HeaderlessSection extends Validate
{
	/**
	 * headerless section
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'headerless_section', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section\>/is", $str, $secs);

		if ( ! $secs[0])
		{
			Validate\Set::log($url, 'headerless_section', self::$unspec, 4);
			return;
		}

		foreach ($secs[0] as $k => $v)
		{
			$tstr = Element\Get\Each::firstTag($v);

			Validate\Set::errorAndLog(
				 ! preg_match("/\<h\d/", $v),
				$url,
				'headerless_section',
				$k,
				$tstr,
				$tstr
			);
		}
		static::addErrorToHtml($url, 'headerless_section', static::$error_ids[$url], 'ignores');
	}
}
