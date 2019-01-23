<?php
/**
 * * A11yc\Validate\Check\JaWordBreakingSpace
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

class JaWordBreakingSpace extends Validate
{
	/**
	 * ja word breaking space
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'ja_word_breaking_space', self::$unspec, 5);
//		if (strpos(Element\Get\Each::lang($url), 'ja') === false) return false;
		if (strpos(A11YC_LANG, 'ja') === false) return false;
		Validate\Set::log($url, 'ja_word_breaking_space', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		// $str = str_replace(array("\n", "\r"), '', $str);

		$search = '[^\x01-\x7E][ 　]{2,}[^\x01-\x7E]'; // MB+spaces+MB
		$search.= '|[^\x01-\x7E][ 　]+[^\x01-\x7E][ 　]'; // MB+space(s)+MB+space
		$search.= '|(?<![^\x01-\x7E])[^\x01-\x7E][ 　]+[^\x01-\x7E](?![^\x01-\x7E])'; // single MB+space(s)+single MB

		preg_match_all("/(".$search.")/iu", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			$tstr = $ms[0][$k];
			Validate\Set::error($url, 'ja_word_breaking_space', $k, $tstr, $m);
		}

		static::addErrorToHtml($url, 'ja_word_breaking_space', static::$error_ids[$url], 'ignores');
	}
}
