<?php
/**
 * * A11yc\Validate\Check\StyleForStructure
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

class StyleForStructure extends Validate
{
	/**
	 * style for structure
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'style_for_structure', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$tstr = $ms[0][$k];
			$attrs = Element\Get::attributes($m);
			if ( ! array_key_exists('style', $attrs)) continue;

			Validate\Set::errorAndLog(
				strpos($attrs['style'], 'size') !== false ||
				strpos($attrs['style'], 'color') !== false, // includes background-color
				$url,
				'style_for_structure',
				$k,
				$tstr,
				$m
			);
		}
		static::addErrorToHtml($url, 'style_for_structure', static::$error_ids[$url], 'ignores');
	}
}
