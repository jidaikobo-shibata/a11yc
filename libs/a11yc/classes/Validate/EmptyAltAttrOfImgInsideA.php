<?php
/**
 * A11yc\Validate\EmptyAltAttrOfImgInsideA
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class EmptyAltAttrOfImgInsideA extends Validate
{
	/**
	 * empty alt attr of img inside a
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = static::ignoreElements(static::$hl_htmls[$url]);

		$ms = static::getElementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2]) return;

		foreach ($ms[2] as $k => $m)
		{
			if (strpos($m, '<img') === false) continue; // without image
			if (static::isIgnorable($ms[0][$k])) continue; // ignorable
			$t = trim(strip_tags($m)); // php <= 5.5 cannot use function return value
			if ( ! empty($t)) continue; // not image only

			$mms = static::getElementsByRe($m, 'ignores', 'imgs', true);
			$alt = '';
			foreach ($mms[0] as $in_img)
			{
				$attrs = static::getAttributes($in_img);
				foreach ($attrs as $kk => $vv)
				{
					if (strpos($kk, 'alt') !== false)
					{
						$alt.= $vv;
					}
				}
			}

			$alt = trim($alt);

			if ( ! $alt)
			{
				static::$error_ids[$url]['empty_alt_attr_of_img_inside_a'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['empty_alt_attr_of_img_inside_a'][$k]['str'] = @basename(@$attrs['src']);
			}
		}

		static::addErrorToHtml($url, 'empty_alt_attr_of_img_inside_a', static::$error_ids[$url], 'ignores');
	}
}
