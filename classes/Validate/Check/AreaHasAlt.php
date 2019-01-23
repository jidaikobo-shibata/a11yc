<?php
/**
 * * A11yc\Validate\Check\AreaHasAlt
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

class AreaHasAlt extends Validate
{
	/**
	 * area has alt
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'area_has_alt', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;
		Validate\Set::log($url, 'area_has_alt', self::$unspec, 0);
		$is_exists = false;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$is_exists = true;

			$attrs = Element\Get::attributes($m);
			$tstr = $ms[0][$k];

			Validate\Set::errorAndLog(
				 ! isset($attrs['alt']) || empty($attrs['alt']),
				$url,
				'area_has_alt',
				$k,
				$tstr,
				basename(Arr::get($attrs, 'href', ''))
			);
		}

		if ( ! $is_exists)
		{
			Validate\Set::log($url, 'area_has_alt', self::$unspec, 4);
		}

		static::addErrorToHtml($url, 'area_has_alt', static::$error_ids[$url], 'ignores');
	}
}
