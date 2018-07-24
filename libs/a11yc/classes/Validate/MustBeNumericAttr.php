<?php
/**
 * A11yc\Validate\MustBeNumericAttr
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class MustBeNumericAttr extends Validate
{
	/**
	 * numeric attr
	 *
	 * @return Bool
	 */
	public static function check($url)
	{
		return; // studying

		static::$logs[$url]['must_be_numeric_attr'][self::$unspec] = 1;
		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0])
		{
			static::$logs[$url]['must_be_numeric_attr'][self::$unspec] = 4;
			return;
		}

		$targets = array(
			'width',
			'height',
			'border',
		);

		foreach ($ms[0] as $k => $v)
		{
			$attrs = Element::getAttributes($v);

			foreach ($attrs as $attr => $val)
			{
				if ( ! in_array($attr, $targets)) continue;

				if ( ! is_numeric($val))
				{
					static::$logs[$url]['must_be_numeric_attr'][$v] = -1;
					static::$error_ids[$url]['must_be_numeric_attr'][$k]['id'] = $v;
					static::$error_ids[$url]['must_be_numeric_attr'][$k]['str'] = $attr;
				}
				else
				{
					static::$logs[$url]['must_be_numeric_attr'][$v] = 2;
				}
			}
		}
		static::addErrorToHtml($url, 'must_be_numeric_attr', static::$error_ids[$url], 'ignores');
	}
}
