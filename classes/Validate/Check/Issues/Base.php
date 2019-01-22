<?php
/**
 * * A11yc\Validate\Check\Issues
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check\Issues;

use A11yc\Element;
use A11yc\Validate;
use A11yc\Model;

class Base extends Validate
{
	/**
	 * elements
	 *
	 * @param String $url
	 * @param String $regex
	 * @return Void
	 */
	public static function check($url, $regex)
	{
		if (A11YC_DB_TYPE == 'none') return;

		$str = Element\Get::ignoredHtml($url);
		$n = 0;
		foreach (Model\Issue::fetchByUrl($url) as $vals)
		{
			foreach ($vals as $v)
			{
				if (
					! empty($v['html']) &&
					preg_match($regex, $v['html']) &&
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
}
