<?php
/**
 * * A11yc\Validate\Check\SameUrlsShouldHaveSameText
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

class SameUrlsShouldHaveSameText extends Validate
{
	/**
	 * same_urls_should_have_same_text
	 * NOTE: some screen readers read anchor's title attribute.
	 * and user cannot understand that title is exist or not.
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'same_urls_should_have_same_text', self::$unspec, 1);

		// urls
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[1])
		{
			Validate\Set::log($url, 'same_urls_should_have_same_text', self::$unspec, 4);
			return;
		}

		$urls = array();
		foreach ($ms[1] as $k => $v)
		{
			if (Element::isIgnorable($ms[0][$k])) continue;

			$attrs = Element\Get::attributes($v);

			if ( ! isset($attrs['href'])) continue;
			$each_url = Util::enuniqueUri($attrs['href']);

			// strip m except for alt
			$text = $ms[2][$k];
			preg_match_all("/\<\w+ +?[^\>]*?alt *?= *?[\"']([^\"']*?)[\"'][^\>]*?\>/", $text, $mms);
			if ($mms)
			{
				foreach (array_keys($mms[0]) as $kk)
				{
					$text = str_replace($mms[0][$kk], $mms[1][$kk], $text);
				}
			}
			$text = strip_tags($text);
			$text = trim($text);
			$text = preg_replace("/\s{2,}/i", ' ', $text);
			$tstr = $ms[0][$k];

			// check
			if ( ! array_key_exists($each_url, $urls))
			{
				$urls[$each_url] = $text;
				Validate\Set::log($url, 'same_urls_should_have_same_text', $tstr, 3);
			}
			// ouch! same text
			else if ($urls[$each_url] != $text)
			{
				Validate\Set::error(
					$url,
					'same_urls_should_have_same_text',
					$k,
					$tstr,
					$each_url.': ('.mb_strlen($urls[$each_url], "UTF-8").') "'.$urls[$each_url].'" OR ('.mb_strlen($text, "UTF-8").') "'.$text.'"'
				);
			}
		}
		static::addErrorToHtml($url, 'same_urls_should_have_same_text', static::$error_ids[$url], 'ignores_comment_out');
	}
}
