<?php
/**
 * * A11yc\Validate\Check\MustBeNumericAttr
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

class MustBeNumericAttr extends Validate
{
	/**
	 * numeric attr
	 *
	 * @return Bool
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'must_be_numeric_attr', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0])
		{
			Validate\Set::log($url, 'must_be_numeric_attr', self::$unspec, 4);
			return;
		}

		$targets = array(
			'width',
			'height',
			'border',
		);

		foreach ($ms[0] as $k => $v)
		{
			$attrs = Element\Get::attributes($v);

			foreach ($attrs as $attr => $val)
			{
				if ( ! in_array($attr, $targets)) continue;


				Validate\Set::errorAndLog(
					 ! is_numeric($val),
					$url,
					'must_be_numeric_attr',
					$k,
					$v,
					$attr
				);
			}
		}
		static::addErrorToHtml($url, 'must_be_numeric_attr', static::$error_ids[$url], 'ignores');
	}
}
