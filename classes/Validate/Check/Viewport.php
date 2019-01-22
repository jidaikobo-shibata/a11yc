<?php
/**
 * * A11yc\Validate\Check\Viewport
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

class Viewport extends Validate
{
	/**
	 * viewport
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'user_scalable_no', self::$unspec, 5);
		if (Validate::$is_partial === true) return;
		Validate\Set::log($url, 'user_scalable_no', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[1] as $k => $tag)
		{
			$tstr = $ms[0][$k];
			Validate\Set::errorAndLog(
				$tag == 'meta' && strpos($ms[2][$k], 'user-scalable=no') !== false,
				$url,
				'user_scalable_no',
				0,
				$tstr,
				'user-scalable=no'
			);
		}
		static::addErrorToHtml($url, 'user_scalable_no', static::$error_ids[$url]);
	}
}
