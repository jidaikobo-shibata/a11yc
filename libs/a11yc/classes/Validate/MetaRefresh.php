<?php
/**
 * A11yc\Validate\MetaRefresh
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class MetaRefresh extends Validate
{
	/**
	 * meta refresh
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if (Validate::isPartial() == true) return;

		$str = Element::ignoreElements(static::$hl_htmls[$url]);
		$ms = Element::getElementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'meta') continue;
			$attrs = Element::getAttributes($v);

			if ( ! array_key_exists('http-equiv', $attrs)) continue;
			if ( ! array_key_exists('content', $attrs)) continue;
			if ( $attrs['http-equiv'] !== 'refresh') continue;

			// ignore zero refresh
			// see http://www.ciaj.or.jp/access/web/docs/WCAG-TECHS/H76.html
			$content = $attrs['content'];
		if (
				trim(substr($content, 0, strpos($content, ';'))) != '0' ||
				(strpos($content, ';') === false && trim($content) != '0')
			)
			{
				static::$error_ids[$url]['meta_refresh'][0]['id'] = $ms[0][$k];
				static::$error_ids[$url]['meta_refresh'][0]['str'] = $ms[0][$k];
			}
		}
		static::addErrorToHtml($url, 'meta_refresh', static::$error_ids[$url], 'ignores');
	}
}
