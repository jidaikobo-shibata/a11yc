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
		static::setLog($url, 'user_scalable_no', self::$unspec, 5);
		if (Validate::$is_partial == true) return;
		static::setLog($url, 'user_scalable_no', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[1] as $k => $tag)
		{
			$tstr = $ms[0][$k];

			if ($tag == 'meta' && strpos($ms[2][$k], 'user-scalable=no') !== false)
			{
				static::setError($url, 'user_scalable_no', 0, $tstr, 'user-scalable=no');
			}
			else
			{
				static::setLog($url, 'user_scalable_no', $tstr, 2);
			}
		}
		static::addErrorToHtml($url, 'user_scalable_no', static::$error_ids[$url]);
	}
}
