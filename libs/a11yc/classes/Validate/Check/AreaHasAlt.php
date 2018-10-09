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

class AreaHasAlt extends Validate
{
	/**
	 * area has alt
	 *
	 * @return  bool
	 */
	public static function check($url)
	{
		static::setLog($url, 'area_has_alt', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;
		static::setLog($url, 'area_has_alt', self::$unspec, 0);
		$is_exists = false;

		foreach ($ms[0] as $k => $m)
		{
			if (substr($m, 0, 5) !== '<area') continue;
			$is_exists = true;

			$attrs = Element\Get::attributes($m);
			$tstr = $ms[0][$k];
			if ( ! isset($attrs['alt']) || empty($attrs['alt']))
			{
				$str = Arr::get($attrs, 'href');
				$str = ! empty($str) ? basename($str) : $str;
				static::setError($url, 'area_has_alt', $k, $tstr, $str);
			}
			else
			{
				static::setLog($url, 'area_has_alt', $tstr, 2);
			}
		}

		if ( ! $is_exists)
		{
			static::setLog($url, 'area_has_alt', self::$unspec, 4);
		}

		static::addErrorToHtml($url, 'area_has_alt', static::$error_ids[$url], 'ignores');
	}
}
