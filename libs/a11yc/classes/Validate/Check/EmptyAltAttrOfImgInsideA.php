<?php
/**
 * * A11yc\Validate\Check\EmptyAltAttrOfImgInsideA
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

class EmptyAltAttrOfImgInsideA extends Validate
{
	/**
	 * empty alt attr of img inside a
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'empty_alt_attr_of_img_inside_a', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2])
		{
			Validate\Set::log($url, 'empty_alt_attr_of_img_inside_a', self::$unspec, 4);
			return;
		}

		foreach ($ms[2] as $k => $m)
		{
			if (strpos($m, '<img') === false) continue; // without image
			if (Element::isIgnorable($ms[0][$k])) continue; // ignorable
			$t = trim(strip_tags($m)); // php <= 5.5 cannot use function return value
			if ( ! empty($t)) continue; // not image only

			$mms = Element\Get::elementsByRe($m, 'ignores', 'imgs', true);
			$alt = '';
			$src = '';
			foreach ($mms[0] as $in_img)
			{
				$attrs = Element\Get::attributes($in_img);
				$alt.= Arr::get($attrs, 'alt', '');
				$src = Arr::get($attrs, 'src', ''); // update by latest
			}
			$src = ! empty($src) ? Util::s(basename($src)) : '';

			$alt = trim($alt);
			$tstr = $ms[0][$k];

			if ( ! $alt)
			{
				Validate\Set::error($url, 'empty_alt_attr_of_img_inside_a', $k, $tstr, $src);
			}
			else
			{
				Validate\Set::log($url, 'empty_alt_attr_of_img_inside_a', $tstr, 2);
			}
		}

		static::addErrorToHtml($url, 'empty_alt_attr_of_img_inside_a', static::$error_ids[$url], 'ignores');
	}
}
