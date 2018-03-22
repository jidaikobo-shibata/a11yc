<?php
/**
 * A11yc\Validate\IsseusElements
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class IsseusElements extends Validate
{
	/**
	 * elements
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$n = 0;
		foreach (Model\Issues::fetchByUrl($url) as $v)
		{
			if (
				preg_match('/\<[^\>]+?\>[^\<]+?\</', $v['html']) &&
				strpos($str, $v['html']) !== false
			)
			{
				// add errors
				$key = static::html2id($v['html']);
				static::$error_ids[$url][$key][$n]['id'] = $v['html'];
				static::$error_ids[$url][$key][$n]['str'] = $v['html'];
				static::addErrorToHtml($url, $key, static::$error_ids[$url], 'ignores', $v['html']);
				$n++;
			}
		}
	}
}
