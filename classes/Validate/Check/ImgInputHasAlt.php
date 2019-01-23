<?php
/**
 * * A11yc\Validate\Check\ImgInputHasAlt
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

class ImgInputHasAlt extends Validate
{
	/**
	 * is img input has alt
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'img_input_has_alt', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach($ms[0] as $k => $m)
		{
			if (substr($m, 0, 6) !== '<input') continue;
			$attrs = Element\Get::attributes($m);
			if ( ! isset($attrs['type'])) continue; // unless type it is recognized as a text at html5
			if (isset($attrs['type']) && $attrs['type'] != 'image') continue;
			$tstr = $ms[0][$k];

			$src = basename(Arr::get($attrs, 'src', ''));

			Validate\Set::errorAndLog(
				 ! isset($attrs['alt']) || empty($attrs['alt']),
				$url,
				'img_input_has_alt',
				$k,
				$tstr,
				$src
			);
		}
		static::addErrorToHtml($url, 'img_input_has_alt', static::$error_ids[$url], 'ignores');
	}
}
