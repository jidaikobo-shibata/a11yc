<?php
/**
 * * A11yc\Validate\Check\UnclosedElements
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;

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
		static::setLog($url, 'unclosed_elements', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		// tags
		preg_match_all("/\<([^\>\n]+?)\</i", $str, $tags);

		if ( ! $tags[0]) return;
		foreach ($tags[0] as $k => $m)
		{
			static::setError($url, 'unclosed_elements', $k, $m, $m);
		}
		static::addErrorToHtml($url, 'unclosed_elements', static::$error_ids[$url], 'ignores');
	}
}
