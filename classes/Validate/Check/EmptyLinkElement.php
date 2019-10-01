<?php
/**
 * * A11yc\Validate\Check\EmptyLinkElement
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

class EmptyLinkElement extends Validate
{
	/**
	 * empty_link_element
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'empty_link_element', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[1]) return;

		foreach ($ms[0] as $k => $m)
		{
			if (strpos($m, 'href') === false) continue;
			if (substr($m, 0, 5) === '<area') continue;

			// img's alt
			$text = Element\Get\Each::textFromElement($ms[2][$k]);

			// aria-labelledby
			if (empty($text))
			{
				$text = self::getAriaLabelledby($ms[0][$k], $str, $text);
			}

			// aria-label
			if (empty($text))
			{
				$text = self::getAriaLabel($ms[0][$k], $text);
			}

			Validate\Set::errorAndLog(
				empty($text),
				$url,
				'empty_link_element',
				$k,
				$ms[0][$k],
				$ms[0][$k]
			);
		}
		static::addErrorToHtml($url, 'empty_link_element', static::$error_ids[$url], 'ignores');
	}

	/**
	 * getAriaLabelledby
	 *
	 * @param String $eles
	 * @param String $str
	 * @param String|Bool $text
	 * @return String
	 */
	private static function getAriaLabelledby($eles, $str, $text = '')
	{
		$text = is_bool($text) ? '' : $text;

		if (strpos($eles, 'aria-labelledby') !== false)
		{
			$eleses = explode('>', $eles);
			foreach ($eleses as $ele)
			{
				if (strpos($ele, 'aria-labelledby') === false) continue;
				$attrs = Element\Get::attributes($ele.">");
				$ids = Arr::get($attrs, 'aria-labelledby');
				if (empty($ids)) continue; // error but not indicate by here.

				foreach (explode(' ', $ids) as $id)
				{
					$eachele = Element\Get::elementById($str, $id);
					$text.= Element\Get\Each::textFromElement($eachele);
				}
			}
		}
		return $text;
	}

	/**
	 * getAriaLabel

	 sample1:
	 <a href="http://example.com" aria-label="foo">
		<span class="fa fa-twitter" aria-label="bar">
			<span aria-label="baz"></span>
		</span>
	 </a>

	 result:
	 sample1 + NVDA: reads "foo". speak from the outside.

	 *
	 * @param String $eles
	 * @param String|Bool $text
	 * @return String
	 */
	private static function getAriaLabel($eles, $text = '')
	{
		$text = is_bool($text) ? '' : $text;

		if (strpos($eles, 'aria-label') !== false)
		{
			$eleses = explode('>', $eles);
			foreach ($eleses as $ele)
			{
				if (strpos($ele, 'aria-label') === false) continue;
				if ( ! empty($text)) continue;
				$attrs = Element\Get::attributes($ele.">");
				$text.= Arr::get($attrs, 'aria-label', '');
			}
		}

		return $text;
	}
}
