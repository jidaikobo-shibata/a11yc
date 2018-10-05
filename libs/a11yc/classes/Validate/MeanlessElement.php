<?php
/**
 * A11yc\Validate\MeanlessElement
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Element;

class MeanlessElement extends Validate
{
	/**
	 * meanless element
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['meanless_element'][self::$unspec] = 1;
		static::$logs[$url]['meanless_element_timing'][self::$unspec] = 1;
		$str = Element\Get::ignoredHtml($url);

		$banneds = array(
			'big',
			'tt',
			'center',
			'font',
			'blink',
			'marquee',
			'b',
			'i',
			'u',
			's',
			'strike',
			'basefont',
		);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$n = 0;
		foreach ($ms[0] as $m)
		{
			foreach ($banneds as $banned)
			{
				preg_match_all('/\<'.$banned.' [^\>]*?\>|\<'.$banned.'\>/', $m, $mms);
				if ( ! $mms[0]) continue;
				foreach ($mms[0] as $tag)
				{
					if (strpos($tag, '<blink') !== false || strpos($tag, '<marquee') !== false )
					{
						static::$logs[$url]['meanless_element_timing'][$tag] = -1;
						static::$error_ids[$url]['meanless_element_timing'][$n]['id'] = $tag;
						static::$error_ids[$url]['meanless_element_timing'][$n]['str'] = $tag;
					}
					else
					{
						static::$logs[$url]['meanless_element'][$tag] = -1;
						static::$error_ids[$url]['meanless_element'][$n]['id'] = $tag;
						static::$error_ids[$url]['meanless_element'][$n]['str'] = $tag;
					}
					$n++;
				}
			}
		}
		static::addErrorToHtml($url, 'meanless_element', static::$error_ids[$url], 'ignores');
	}
}
