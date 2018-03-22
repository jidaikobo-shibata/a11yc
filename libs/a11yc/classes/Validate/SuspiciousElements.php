<?php
/**
 * A11yc\Validate\SuspiciousElements
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class SuspiciousElements extends Validate
{
	/**
	 * suspicious elements
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		// tags
		preg_match_all("/\<([^\> \n]+)/i", $str, $tags);

		// elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command', 'frame', 'keygen', 'rect', 'circle', 'line');
		$ignores = array('!doctype', 'html', '![if', '![endif]', '?xml');
		$omissionables = array('li', 'dt', 'dd', 'p', 'rt', 'rp', 'optgroup', 'option', 'tr', 'td', 'th', 'thead', 'tfoot', 'tbody', 'colgroup');
		$ignores = array_merge($ignores, $endless, $omissionables);

		// tags
		$opens = array();
		$ends = array();
		foreach ($tags[1] as $tag)
		{
			$tag = strtolower($tag);
			if (in_array($tag, $ignores)) continue; // ignore
			if (in_array(substr($tag, 1), $ignores)) continue; // ignore

			// collect tags
			if ($tag[0] =='/')
			{
				$ends[] = substr($tag, 1);
				continue;
			}
			$opens[] = $tag;
		}

		// count tags
		$opens_cnt = array_count_values($opens);
		$ends_cnt = array_count_values($ends);

		// check nums of opens
		$too_much_opens = array();
		$too_much_ends = array();
		foreach (array_keys($opens_cnt) as $tag)
		{
			if ( ! isset($ends_cnt[$tag]) || $opens_cnt[$tag] > $ends_cnt[$tag])
			{
				$too_much_opens[] = $tag;
			}
			elseif ($opens_cnt[$tag] < $ends_cnt[$tag])
			{
				$too_much_ends[] = $tag;
			}
		}

		// endless
		$suspicious_ends = array();
		foreach ($endless as $v)
		{
			if (strpos($str, '</'.$v) !== false)
			{
				$suspicious_ends[] = '/'.$v;
			}
		}

		// add errors
		foreach ($too_much_opens as $k => $v)
		{
			static::$error_ids[$url]['too_much_opens'][$k]['id'] = false;
			static::$error_ids[$url]['too_much_opens'][$k]['str'] = $v;
		}
		static::addErrorToHtml($url, 'too_much_opens', static::$error_ids[$url], 'ignores');

		foreach ($too_much_ends as $k => $v)
		{
			static::$error_ids[$url]['too_much_ends'][$k]['id'] = false;
			static::$error_ids[$url]['too_much_ends'][$k]['str'] = $v;
		}
		static::addErrorToHtml($url, 'too_much_ends', static::$error_ids[$url], 'ignores');

		foreach ($suspicious_ends as $k => $v)
		{
			static::$error_ids[$url]['suspicious_ends'][$k]['id'] = false;
			static::$error_ids[$url]['suspicious_ends'][$k]['str'] = $v;
		}
		static::addErrorToHtml($url, 'suspicious_ends', static::$error_ids[$url], 'ignores');
	}
}
