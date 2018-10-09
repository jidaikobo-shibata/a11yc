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

class AltAttrOfImg extends Validate
{
	/**
	 * alt attr of img
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::setLog($url, 'alt_attr_of_img', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');

		if ( ! $ms[1])
		{
			static::setLog($url, 'alt_attr_of_img', self::$unspec, 4);
			return;
		}
		static::setLog($url, 'alt_attr_of_img', self::$unspec, 0);

		foreach ($ms[0] as $k => $m)
		{
			$tstr = $m;
			static::setLog($url, 'alt_attr_of_img', $tstr, 1);

			// alt_attr_of_img
			$attrs = Element\Get::attributes($m);
			$file = Arr::get($attrs, 'src');
			$file = ! empty($file) ? basename($file) : $file;
			if ( ! array_key_exists('alt', $attrs))
			{
				static::setError($url, 'alt_attr_of_img', $k, $tstr, $file);
				// below here alt attribute has to exist.
				continue;
			}
			static::setLog($url, 'alt_attr_of_img', $tstr, 2);

			// role presentation
			if (isset($attrs['role']) && $attrs['role'] == 'presentation') continue;

			// alt_attr_of_blank_only
			static::$logs[$url]['alt_attr_of_blank_only'][$tstr] = 1;
			if (preg_match('/^[ 　]+?$/', $attrs['alt']))
			{
				static::setError($url, 'alt_attr_of_blank_only', $k, $tstr, $file);
			}
			else
			{
				static::setLog($url, 'alt_attr_of_blank_only', $tstr, 2);
			}

			// alt_attr_of_empty
			static::$logs[$url]['alt_attr_of_empty'][$tstr] = 1;
			if (empty($attrs['alt']))
			{
				static::setError($url, 'alt_attr_of_empty', $k, $tstr, $file);
			}
			else
			{
				static::setLog($url, 'alt_attr_of_empty', $tstr, 2);
			}
		}

		static::addErrorToHtml($url, 'alt_attr_of_empty', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_img', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_blank_only', static::$error_ids[$url], 'ignores');
	}
}
