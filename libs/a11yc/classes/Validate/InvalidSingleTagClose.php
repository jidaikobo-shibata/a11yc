<?php
/**
 * A11yc\Validate\InvalidSingleTagClose
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class InvalidSingleTagClose extends Validate
{
	/**
	 * invalid single tag close
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if (preg_match("/[^ ]+\/\>/", $v))
			{
				static::$error_ids[$url]['invalid_single_tag_close'][$k]['id'] = $v;
				static::$error_ids[$url]['invalid_single_tag_close'][$k]['str'] = $ms[1][$k];
			}
		}
		static::addErrorToHtml($url, 'invalid_single_tag_close', static::$error_ids[$url], 'ignores');
	}
}
