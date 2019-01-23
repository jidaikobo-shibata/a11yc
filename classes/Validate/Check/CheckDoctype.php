<?php
/**
 * * A11yc\Validate\Check\CheckDoctype
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

class CheckDoctype extends Validate
{
	/**
	 * check doctype
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if (Validate::$is_partial === true)
		{
			Validate\Set::log($url, 'check_doctype', self::$unspec, 5);
			return;
		}

		Validate\Set::errorAndLog(
			is_null(Element\Get\Each::doctype($url)),
			$url,
			'check_doctype',
			0,
			'',
			'doctype not found'
		);
		static::addErrorToHtml($url, 'check_doctype', static::$error_ids[$url]);
	}
}
