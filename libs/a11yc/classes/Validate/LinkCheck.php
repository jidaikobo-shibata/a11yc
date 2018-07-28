<?php
/**
 * A11yc\Validate\LinkCheck
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class LinkCheck extends Validate
{
	/**
	 * check
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::$logs[$url]['link_check'][self::$unspec] = 5;
		if ( ! static::$do_link_check) return;
		static::$logs[$url]['link_check'][self::$unspec] = 1;

		$str = Element::ignoreElements($url);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		// candidates
		$checks = array(
			'a',
			'img',
			'form',
			'meta',
		);

		// fragments
		preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);

		foreach ($ms[0] as $k => $tag)
		{
			$url = '';
			$ele = $ms[1][$k];

			if ( ! in_array($ele, $checks)) continue;
			$attrs = Element::getAttributes($tag);

			if (isset($attrs['href']))
			{
				$url = $attrs['href'];
			}
			elseif (isset($attrs['src']))
			{
				$url = $attrs['src'];
			}
			elseif (isset($attrs['action']))
			{
				$url = $attrs['action'];
			}
			elseif (isset($attrs['property']))
			{
				if ($attrs['property'] == 'og:url' || $attrs['property'] == 'og:image')
				{
					$url = $attrs['content'];
				}
			}
			else
			{
				continue;
			}

			if ( ! $url) continue;

			// fragments
			if ($url[0] == '#')
			{
				if ( ! in_array(substr($url, 1), $fragments[1]))
				{
					static::$logs[$url]['link_check'][$tag] = -1;
					static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
					static::$error_ids[$url]['link_check'][$k]['str'] = 'Fragment Not Found: '.$url;
				}
				continue;
			}

			// correct url
			if (Element::isIgnorable($tag)) continue;

			$url = Util::enuniqueUri($url);

			// remove strange ampersand. seems depend on environment ?-(
			$url = str_replace('&#038;', '&', $url);

			// links
			if ( ! Crawl::isPageExist($url))
			{
				static::$logs[$url]['link_check'][$tag] = -1;
				static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
				static::$error_ids[$url]['link_check'][$k]['str'] = 'Not Found: '.$tag;
				continue;
			}

			// 40x
			static::$logs[$url]['link_check'][$tag] = -1;
			static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
			static::$error_ids[$url]['link_check'][$k]['str'] = 'header 40x: '.$url;
		}

		if (isset(static::$error_ids[$url]))
		{
			static::addErrorToHtml($url, 'link_check', static::$error_ids[$url], 'ignores_comment_out');
		}
	}
}
