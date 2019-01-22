<?php
/**
 * * A11yc\Validate\Check\SuspiciousAttributes
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

class SuspiciousAttributes extends Validate
{
	/**
	 * suspicious attributes
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'suspicious_attributes', self::$unspec, 1);
		Validate\Set::log($url, 'duplicated_attributes', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$tstr = $ms[0][$k];
			$attrs = Element\Get::attributes($m);

			// suspicious attributes
			$exp = isset($attrs['suspicious']);
			if ($exp)
			{
				Validate\Set::errorAndLog(
					$exp,
					$url,
					'suspicious_attributes',
					$k,
					$tstr,
					join(', ', $attrs['suspicious'])
				);
			}

			// no_space_between_attributes
			Validate\Set::errorAndLog(
				isset($attrs['no_space_between_attributes']) && $attrs['no_space_between_attributes'],
				$url,
				'suspicious_attributes',
				$k,
				$tstr,
				$tstr
			);

			// duplicated_attributes
			Validate\Set::errorAndLog(
				isset($attrs['plural']),
				$url,
				'duplicated_attributes',
				$k,
				$tstr,
				$m
			);
		}
		static::addErrorToHtml($url, 'suspicious_attributes', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_attributes', static::$error_ids[$url], 'ignores');
	}
}
