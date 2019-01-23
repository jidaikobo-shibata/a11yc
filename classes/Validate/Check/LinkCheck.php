<?php
/**
 * * A11yc\Validate\Check\LinkCheck
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

class LinkCheck extends Validate
{
	/**
	 * check
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'link_check', self::$unspec, 5);
		if ( ! static::$do_link_check) return;
		Validate\Set::log($url, 'link_check', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
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
			$ele = $ms[1][$k];

			if ( ! in_array($ele, $checks)) continue;
			$attrs = Element\Get::attributes($tag);

			$target_url = self::getTargetUrl($attrs);
			if ( ! $target_url) continue;

			// fragments
			if ($target_url[0] == '#')
			{
				if ( ! in_array(substr($target_url, 1), $fragments[1]))
				{
					Validate\Set::error($url, 'link_check', $k, $tag, 'Fragment Not Found: '.$target_url);
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
				Validate\Set::error($url, 'link_check', $k, $tag, 'Not Found: '.$target_url);
				continue;
			}

			// 40x
			$headers = @get_headers($target_url);
			if ($headers !== false && strpos($headers[0], ' 40') !== false)
			{
				Validate\Set::error($url, 'link_check', $k, $tag, 'header 40x: '.$target_url);
			}
		}

		if (isset(static::$error_ids[$url]))
		{
			static::addErrorToHtml($url, 'link_check', static::$error_ids[$url], 'ignores_comment_out');
		}
	}

	/**
	 * get target url
	 *
	 * @param Array $attrs
	 * @return String
	 */
	protected static function getTargetUrl($attrs)
	{
		if (isset($attrs['href']))
		{
			return $attrs['href'];
		}
		elseif (isset($attrs['src']))
		{
			return $attrs['src'];
		}
		elseif (isset($attrs['action']))
		{
			return $attrs['action'];
		}
		elseif (isset($attrs['property']))
		{
			if ($attrs['property'] == 'og:url' || $attrs['property'] == 'og:image')
			{
				return $attrs['content'];
			}
		}
		return '';
	}
}
