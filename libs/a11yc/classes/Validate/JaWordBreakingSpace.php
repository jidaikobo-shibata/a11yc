<?php
/**
 * A11yc\Validate\JaWordBreakingSpace
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class JaWordBreakingSpace extends Validate
{
	/**
	 * ja word breaking space
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', static::$hl_htmls[$url]);
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$search = '[^\x01-\x7E][ 　]{2,}[^\x01-\x7E]'; // MB+spaces+MB
		$search.= '|[^\x01-\x7E][ 　]+[^\x01-\x7E][ 　]'; // MB+space(s)+MB+space
		$search.= '|(?<![^\x01-\x7E])[^\x01-\x7E][ 　]+[^\x01-\x7E](?![^\x01-\x7E])'; // single MB+space(s)+single MB

		preg_match_all("/(".$search.")/iu", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids[$url]['ja_word_breaking_space'][$k]['id'] = $ms[0][$k];
			static::$error_ids[$url]['ja_word_breaking_space'][$k]['str'] = $m;
		}
		static::addErrorToHtml($url, 'ja_word_breaking_space', static::$error_ids[$url], 'ignores');
	}
}
