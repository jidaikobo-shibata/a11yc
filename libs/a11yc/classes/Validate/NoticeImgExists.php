<?php
/**
 * A11yc\Validate\NoticeImgExists
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class NoticeImgExists extends Validate
{
	/**
	 * img exists
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['notice_img_exists'][self::$unspec] = 1;
		$str = Element::ignoreElements($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		static::$error_ids[$url]['notice_img_exists'][0]['id'] = 0;
		static::$error_ids[$url]['notice_img_exists'][0]['str'] = A11YC_LANG_IMAGE.' '.sprintf(A11YC_LANG_COUNT_ITEMS, count($ms[1]));
		static::addErrorToHtml($url, 'notice_img_exists', static::$error_ids[$url], 'ignores');
	}
}
