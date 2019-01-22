<?php
/**
 * * A11yc\Validate\Check\NoticeImgExists
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

class NoticeImgExists extends Validate
{
	/**
	 * img exists
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'notice_img_exists', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		$tstr = A11YC_LANG_IMAGE.' '.sprintf(A11YC_LANG_COUNT_ITEMS, count($ms[1]));
		Validate\Set::error($url, 'notice_img_exists', 0, '', $tstr);
		static::addErrorToHtml($url, 'notice_img_exists', static::$error_ids[$url], 'ignores');
	}
}
