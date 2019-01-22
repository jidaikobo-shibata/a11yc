<?php
/**
 * * A11yc\Validate\Check\MetaRefresh
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

class MetaRefresh extends Validate
{
	/**
	 * meta refresh
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'meta_refresh', self::$unspec, 5);
		if (Validate::$is_partial === true) return;
		Validate\Set::log($url, 'meta_refresh', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'meta') continue;
			$attrs = Element\Get::attributes($v);

			if ( ! array_key_exists('http-equiv', $attrs)) continue;
			if ( ! array_key_exists('content', $attrs)) continue;
			if ( $attrs['http-equiv'] !== 'refresh') continue;

			$tstr = $ms[0][$k];

			// ignore zero refresh
			// see http://www.ciaj.or.jp/access/web/docs/WCAG-TECHS/H76.html
			$content = $attrs['content'];
			Validate\Set::errorAndLog(
				trim(substr($content, 0, strpos($content, ';'))) != '0' ||
				(strpos($content, ';') === false && trim($content) != '0'),
				$url,
				'meta_refresh',
				0,
				$tstr,
				$tstr
			);
		}
		static::addErrorToHtml($url, 'meta_refresh', static::$error_ids[$url], 'ignores');
	}
}
