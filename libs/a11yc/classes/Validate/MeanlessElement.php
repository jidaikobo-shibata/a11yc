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
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

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

		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
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
						static::$error_ids[$url]['meanless_element_timing'][$n]['id'] = $tag;
						static::$error_ids[$url]['meanless_element_timing'][$n]['str'] = $tag;
					}
					else
					{
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
