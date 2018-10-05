<?php
/**
 * A11yc\Validate\SuspiciousAttributes
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Element;

class SuspiciousAttributes extends Validate
{
	/**
	 * suspicious attributes
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['suspicious_attributes'][self::$unspec] = 1;
		static::$logs[$url]['duplicated_attributes'][self::$unspec] = 1;
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$tstr = $ms[0][$k];
			$attrs = Element\Get::attributes($m);

			// suspicious attributes
			if (isset($attrs['suspicious']))
			{
				static::$logs[$url]['suspicious_attributes'][$tstr] = -1;
				static::$error_ids[$url]['suspicious_attributes'][$k]['id'] = $tstr;
				static::$error_ids[$url]['suspicious_attributes'][$k]['str'] = join(', ', $attrs['suspicious']);
			}
			else
			{
				static::$logs[$url]['suspicious_attributes'][$tstr] = 2;
			}

			// no_space_between_attributes
			if (isset($attrs['no_space_between_attributes']) && $attrs['no_space_between_attributes'])
			{
				static::$logs[$url]['no_space_between_attributes'][$tstr] = -1;
				static::$error_ids[$url]['no_space_between_attributes'][$k]['id'] = $tstr;
				static::$error_ids[$url]['no_space_between_attributes'][$k]['str'] = $tstr;
			}
			else
			{
				static::$logs[$url]['no_space_between_attributes'][$tstr] = 2;
			}

			// duplicated_attributes
			if (isset($attrs['plural']))
			{
				static::$logs[$url]['duplicated_attributes'][$tstr] = -1;
				static::$error_ids[$url]['duplicated_attributes'][$k]['id'] = $tstr;
				static::$error_ids[$url]['duplicated_attributes'][$k]['str'] = $m;
			}
			else
			{
				static::$logs[$url]['duplicated_attributes'][$tstr] = 2;
			}
		}
		static::addErrorToHtml($url, 'suspicious_attributes', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_attributes', static::$error_ids[$url], 'ignores');
	}
}
