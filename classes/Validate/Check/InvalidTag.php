<?php
/**
 * * A11yc\Validate\Check\InvalidTag
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

class InvalidTag extends Validate
{
	/**
	 * invalid tag
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'cannot_contain_newline', self::$unspec, 1);
		Validate\Set::log($url, 'unbalanced_quotation', self::$unspec, 1);
		Validate\Set::log($url, 'cannot_contain_multibyte_space', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			// newline character must not exists in attr
			// thx https://twitter.com/sbtnbfm/status/948881557233713152
			$attrs = Element\Get::attributes($m);
			$tstr = $ms[0][$k];

			foreach ($attrs as $val)
			{
				if (strpos($val, "\n") !== false)
				{
					Validate\Set::error($url, 'cannot_contain_newline', $k, $tstr, $m);
					break;
				}
				else
				{
					Validate\Set::log($url, 'cannot_contain_newline', $tstr, 2);
				}
			}

			// unbalanced_quotation
			// delete qouted quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);

			// in Englsih, single quote is frequent on grammar
			// if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			if (substr_count($tag, '"') % 2 !== 0)
			{
				Validate\Set::error($url, 'unbalanced_quotation', $k, $tstr, $m);
			}
			else
			{
				Validate\Set::log($url, 'unbalanced_quotation', $tstr, 2);
			}

			if (A11YC_LANG != 'ja') continue;

			// multi-byte space
			// ignore values of attributes
			$tag = preg_replace("/(\".+?\"|'.+?')/is", '', $tag);

			if (strpos($tag, '　') !== false)
			{
				Validate\Set::error($url, 'cannot_contain_multibyte_space', $k, $tstr, $m);
			}
			else
			{
				Validate\Set::log($url, 'cannot_contain_multibyte_space', $tstr, 2);
			}
		}
		static::addErrorToHtml($url, 'unbalanced_quotation', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'cannot_contain_multibyte_space', static::$error_ids[$url], 'ignores');
		static::addErrorToHtml($url, 'cannot_contain_newline', static::$error_ids[$url], 'ignores');
	}
}
