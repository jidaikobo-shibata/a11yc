<?php
/**
 * A11yc\Validate\UnclosedElements
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class UnclosedElements extends Validate
{
	/**
	 * unclosed_elements
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['unclosed_elements'][self::$unspec] = 1;
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		// tags
		preg_match_all("/\<([^\>\n]+?)\</i", $str, $tags);

		if ( ! $tags[0]) return;
		foreach ($tags[0] as $k => $m)
		{
			static::$logs[$url]['unclosed_elements'][$m] = -1;
			static::$error_ids[$url]['unclosed_elements'][$k]['id'] = $m;
			static::$error_ids[$url]['unclosed_elements'][$k]['str'] = $m;
		}
		static::addErrorToHtml($url, 'unclosed_elements', static::$error_ids[$url], 'ignores');
	}
}
