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
			$target_url = '';
			$ele = $ms[1][$k];

			if ( ! in_array($ele, $checks)) continue;
			$attrs = Element::getAttributes($tag);

			if (isset($attrs['href']))
			{
				$target_url = $attrs['href'];
			}
			elseif (isset($attrs['src']))
			{
				$target_url = $attrs['src'];
			}
			elseif (isset($attrs['action']))
			{
				$target_url = $attrs['action'];
			}
			elseif (isset($attrs['property']))
			{
				if ($attrs['property'] == 'og:url' || $attrs['property'] == 'og:image')
				{
					$target_url = $attrs['content'];
				}
			}
			else
			{
				continue;
			}

			if ( ! $target_url) continue;

			// fragments
			if ($target_url[0] == '#')
			{
				if ( ! in_array(substr($target_url, 1), $fragments[1]))
				{
					static::$logs[$url]['link_check'][$tag] = -1;
					static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
					static::$error_ids[$url]['link_check'][$k]['str'] = 'Fragment Not Found: '.$target_url;
				}
				continue;
			}

			// correct url
			if (Element::isIgnorable($tag)) continue;

			$target_url = Util::enuniqueUri($target_url);

			// remove strange ampersand. seems depend on environment ?-(
			$target_url = str_replace('&#038;', '&', $target_url);

			// links
			if ( ! Crawl::isPageExist($target_url))
			{
				static::$logs[$url]['link_check'][$tag] = -1;
				static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
				static::$error_ids[$url]['link_check'][$k]['str'] = 'Not Found: '.$target_url;
				continue;
			}

			// 40x
			$headers = @get_headers($target_url);
			if ($headers !== false && strpos($headers[0], ' 40') !== false)
			{
				static::$logs[$url]['link_check'][$tag] = -1;
				static::$error_ids[$url]['link_check'][$k]['id'] = $tag;
				static::$error_ids[$url]['link_check'][$k]['str'] = 'header 40x: '.$target_url;
			}
		}

		if (isset(static::$error_ids[$url]))
		{
			static::addErrorToHtml($url, 'link_check', static::$error_ids[$url], 'ignores_comment_out');
		}
	}
}
