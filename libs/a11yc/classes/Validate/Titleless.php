<?php
/**
 * A11yc\Validate\Titleless
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class Titleless extends Validate
{
	/**
	 * titleless
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['titleless'][self::$unspec] = 5;
		if (Validate::$is_partial == true) return;
		static::$logs[$url]['titleless'][self::$unspec] = 1;

		$str = Element::ignoreElements($url);

		// to locate first element at the error
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');

		if (
			strpos(strtolower($str), '<title') === false || // lacknesss of title element
			preg_match("/\<title[^\>]*?\>[ 　]*?\<\/title/si", $str) // lacknesss of title
		)
		{
			static::$logs[$url]['titleless'][self::$unspec] = -1;
			static::$error_ids[$url]['titleless'][0]['id'] = false;
			static::$error_ids[$url]['titleless'][0]['str'] = $ms[0][0];
		}
		else
		{
			static::$logs[$url]['titleless'][self::$unspec] = 2;
		}

		static::addErrorToHtml($url, 'titleless', static::$error_ids[$url], 'ignores');
	}
}
