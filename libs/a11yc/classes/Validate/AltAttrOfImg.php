<?php
/**
 * A11yc\Validate\AltAttrOfImg
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

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
		$str = static::ignoreElements(static::$hl_htmls[$url]);

		$ms = static::getElementsByRe($str, 'ignores', 'imgs');
		if ( ! $ms[1]) return;

		foreach ($ms[1] as $k => $m)
		{
			// alt_attr_of_img
			$attrs = static::getAttributes($m);
			if ( ! array_key_exists('alt', $attrs))
			{
				static::$error_ids[$url]['alt_attr_of_img'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['alt_attr_of_img'][$k]['str'] = @basename(@$attrs['src']);

				// below here alt attribute has to exist.
				continue;
			}

			// role presentation
			if (isset($attrs['role']) && $attrs['role'] == 'presentation') continue;

			// alt_attr_of_blank_only
			if (preg_match('/^[ 　]+?$/', $attrs['alt']))
			{
				static::$error_ids[$url]['alt_attr_of_blank_only'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['alt_attr_of_blank_only'][$k]['str'] = @basename(@$attrs['src']);
			}

			// alt_attr_of_empty
			if (empty($attrs['alt']))
			{
				static::$error_ids[$url]['alt_attr_of_empty'][$k]['id'] = $ms[0][$k];
				static::$error_ids[$url]['alt_attr_of_empty'][$k]['str'] = @basename(@$attrs['src']);
			}
		}
		static::addErrorToHtml($url, 'alt_attr_of_empty', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_img', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'alt_attr_of_blank_only', static::$error_ids[$url], 'ignores');
	}
}
