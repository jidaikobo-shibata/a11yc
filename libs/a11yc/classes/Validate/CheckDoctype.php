<?php
/**
 * A11yc\Validate\CheckDoctype
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class CheckDoctype extends Validate
{
	/**
	 * check doctype
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if (Validate::$is_partial == true)
		{
			static::$logs[$url]['check_doctype'][self::$unspec] = 5;
			return;
		}
		static::$logs[$url]['check_doctype'][self::$unspec] = 1;

		if (is_null(Element\Get::doctype($url)))
		{
			static::$logs[$url]['check_doctype'][self::$unspec] = -1;
			static::$error_ids[$url]['check_doctype'][0]['id'] = false;
			static::$error_ids[$url]['check_doctype'][0]['str'] = 'doctype not found';
		}
		else
		{
			static::$logs[$url]['check_doctype'][self::$unspec] = 2;
		}
		static::addErrorToHtml($url, 'check_doctype', static::$error_ids[$url]);
	}
}
