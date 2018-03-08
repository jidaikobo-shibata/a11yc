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
		$str = static::ignoreElements(static::$hl_htmls[$url]);

		$ms = static::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::getAttributes($m);

			// suspicious attributes
			if (isset($attrs['suspicious']))
			{
				static::$error_ids[$url]['suspicious_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['suspicious_attributes'][$k]['str'] = join(', ', $attrs['suspicious']);
			}

			// duplicated_attributes
			if (isset($attrs['plural']))
			{
				static::$error_ids[$url]['duplicated_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['duplicated_attributes'][$k]['str'] = $m;
			}
		}
		static::addErrorToHtml($url, 'suspicious_attributes', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'duplicated_attributes', static::$error_ids[$url], 'ignores');
	}
}
