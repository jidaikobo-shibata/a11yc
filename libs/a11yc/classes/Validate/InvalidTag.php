<?php
/**
 * A11yc\Validate\InvalidTag
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class InvalidTag extends Validate
{
	/**
	 * invalid tag
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['cannot_contain_newline'][self::$unspec] = 1;
		static::$logs[$url]['unbalanced_quotation'][self::$unspec] = 1;
		static::$logs[$url]['cannot_contain_multibyte_space'][self::$unspec] = 1;
		$str = Element::ignoreElements($url);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			// newline character must not exists in attr
			// thx https://twitter.com/sbtnbfm/status/948881557233713152
			$attrs = Element::getAttributes($m);
			$tstr = $ms[0][$k];

			foreach ($attrs as $val)
			{
				if (strpos($val, "\n") !== false)
				{
					static::$logs[$url]['cannot_contain_newline'][$tstr] = -1;
					static::$error_ids[$url]['cannot_contain_newline'][$k]['id'] = $tstr;
					static::$error_ids[$url]['cannot_contain_newline'][$k]['str'] = $m;
					break;
				}
				else
				{
					static::$logs[$url]['cannot_contain_newline'][$tstr] = 2;
				}
			}

			// unbalanced_quotation
			// delete qouted quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);

			// in Englsih, single quote is frequent on grammar
			// if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			if (substr_count($tag, '"') % 2 !== 0)
			{
				static::$logs[$url]['unbalanced_quotation'][$tstr] = -1;
				static::$error_ids[$url]['unbalanced_quotation'][$k]['id'] = $tstr;
				static::$error_ids[$url]['unbalanced_quotation'][$k]['str'] = $m;
			}
			else
			{
				static::$logs[$url]['unbalanced_quotation'][$tstr] = 2;
			}

			if (A11YC_LANG != 'ja') continue;

			// multi-byte space
			// ignore values of attributes
			$tag = preg_replace("/(\".+?\"|'.+?')/is", '', $tag);

			if (strpos($tag, '　') !== false)
			{
				static::$logs[$url]['cannot_contain_multibyte_space'][$tstr] = 1;
				static::$error_ids[$url]['cannot_contain_multibyte_space'][$k]['id'] = $tstr;
				static::$error_ids[$url]['cannot_contain_multibyte_space'][$k]['str'] = $m;
			}
			else
			{
				static::$logs[$url]['cannot_contain_multibyte_space'][$tstr] = 2;
			}
		}
		static::addErrorToHtml($url, 'unbalanced_quotation', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'cannot_contain_multibyte_space', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'cannot_contain_newline', static::$error_ids[$url], 'ignores');
	}
}
