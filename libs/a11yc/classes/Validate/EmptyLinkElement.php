<?php
/**
 * A11yc\Validate\EmptyLinkElement
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class EmptyLinkElement extends Validate
{
	/**
	 * empty_link_element
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$ms = Element::getElementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[1]) return;

		foreach ($ms[0] as $k => $m)
		{
			if (strpos($m, 'href') === false) continue;

			// img's alt
			$text = '';
			if (strpos($ms[2][$k], 'img') !== false)
			{
				$imgs = explode('>', $ms[2][$k]);
				foreach ($imgs as $img)
				{
					if (strpos($img, 'img') === false) continue;
					$attrs = Element::getAttributes($img.">");

					foreach ($attrs as $kk => $vv)
					{
						if (strpos($kk, 'alt') !== false)
						{
							$text.= $vv;
						}
					}
				}
				$text = trim($text);
			}

			$text.= strip_tags($m);

			if (empty($text))
			{
				static::$error_ids[$url]['empty_link_element'][0]['id'] = $ms[0][$k];
				static::$error_ids[$url]['empty_link_element'][0]['str'] = Util::s($ms[0][$k]);
			}
		}
		static::addErrorToHtml($url, 'empty_link_element', static::$error_ids[$url], 'ignores');
	}
}
