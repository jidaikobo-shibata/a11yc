<?php
/**
 * * A11yc\Validate\Check\SuspiciousElements
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

class SuspiciousElements extends Validate
{
	/**
	 * suspicious elements
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'too_much_opens', self::$unspec, 1);
		Validate\Set::log($url, 'too_much_ends', self::$unspec, 1);
		Validate\Set::log($url, 'suspicious_ends', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		// tags
		preg_match_all("/\<([^\> \n]+)/i", $str, $tags);

		// elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command', 'frame', 'keygen');
		$ignores = array('!doctype', 'html', '![if', '![endif]', '?xml');
		$omissionables = array('li', 'dt', 'dd', 'p', 'rt', 'rp', 'optgroup', 'option', 'tr', 'td', 'th', 'thead', 'tfoot', 'tbody', 'colgroup',
		// svg
		'path', 'rect', 'line', 'polygon', 'circle', 'ellipse', 'text', 'use', 'image');
		$ignores = array_merge($ignores, $endless, $omissionables);

		// count tags
		list($too_much_opens, $too_much_ends) = self::countTags($tags, $ignores);

		// endless
		$suspicious_ends = self::suspiciousEnds($endless, $str);

		// add errors
		foreach ($too_much_opens as $k => $v)
		{
			Validate\Set::error($url, 'too_much_opens', $k, '', $v);
		}
		static::addErrorToHtml($url, 'too_much_opens', static::$error_ids[$url], 'ignores');

		foreach ($too_much_ends as $k => $v)
		{
			Validate\Set::error($url, 'too_much_ends', $k, '', $v);
		}
		static::addErrorToHtml($url, 'too_much_ends', static::$error_ids[$url], 'ignores');

		foreach ($suspicious_ends as $k => $v)
		{
			Validate\Set::error($url, 'suspicious_ends', $k, '', $v);
		}
		static::addErrorToHtml($url, 'suspicious_ends', static::$error_ids[$url], 'ignores');
	}

	/**
	 * count tags
	 *
	 * @param Array|Null $tags
	 * @param Array $ignores
	 * @return Array
	 */
	public static function countTags($tags, $ignores)
	{
		$tags = is_null($tags) ? array() : $tags;

		$opens = array();
		$ends = array();
		foreach ($tags[1] as $tag)
		{
			$tag = strtolower($tag);
			$tag = rtrim($tag, '/');
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
		return array($too_much_opens, $too_much_ends);
	}

	/**
	 * suspicious ends
	 *
	 * @param Array $endless
	 * @param String $str
	 * @return Array
	 */
	public static function suspiciousEnds($endless, $str)
	{
		$suspicious_ends = array();
		foreach ($endless as $v)
		{
			if (strpos($str, '</'.$v.'>') !== false)
			{
				$suspicious_ends[] = '/'.$v;
			}
		}
		return $suspicious_ends;
	}
}
