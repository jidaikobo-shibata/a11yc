<?php
/**
 * A11yc\Validate\Check\AltAttrOfImg
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

class AltAttrOfImg extends Validate
{
	/**
	 * alt attr of img
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'alt_attr_of_img', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');

		if ( ! $ms[1])
		{
			Validate\Set::log($url, 'alt_attr_of_img', self::$unspec, 4);
			return;
		}
		Validate\Set::log($url, 'alt_attr_of_img', self::$unspec, 0);

		foreach ($ms[0] as $k => $m)
		{
			$tstr = $m;
			Validate\Set::log($url, 'alt_attr_of_img', $tstr, 1);

			// alt_attr_of_img
			$attrs = Element\Get::attributes($m);
			$file = Arr::get($attrs, 'src');
			$file = ! empty($file) ? basename($file) : $file;

			// below here alt attribute has to exist.
			if ( ! array_key_exists('alt', $attrs))
			{
				Validate\Set::error($url, 'alt_attr_of_img', $k, $tstr, $file);
				continue;
			}
			Validate\Set::log($url, 'alt_attr_of_img', $tstr, 2);

			// role presentation
			if (Arr::get($attrs, 'role') == 'presentation') continue;

			// alt_attr_of_blank_only
			Validate\Set::errorAndLog(
				preg_match('/^[ 　]+?$/', $attrs['alt']),
				$url,
				'alt_attr_of_blank_only',
				$k,
				$tstr,
				$file
			);

			// alt_attr_of_empty
			Validate\Set::errorAndLog(
				empty($attrs['alt']),
				$url,
				'alt_attr_of_empty',
				$k,
				$tstr,
				$file
			);
		}

		static::addErrorToHtml($url, 'alt_attr_of_empty', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_img', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_blank_only', static::$error_ids[$url], 'ignores');
	}
}
