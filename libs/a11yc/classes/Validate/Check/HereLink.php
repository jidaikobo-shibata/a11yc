<?php
/**
 * * A11yc\Validate\Check\HereLink
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

class HereLink extends Validate
{
	/**
	 * here link
	 *
	 * @return  void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'here_link', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2])
		{
			Validate\Set::log($url, 'here_link', self::$unspec, 4);
			return;
		}

		$heres = array_map('trim', explode(',', A11YC_LANG_HERE));
		foreach ($ms[2] as $k => $m)
		{
			Validate\Set::errorAndLog(
				in_array(strtolower($m), $heres),
				$url,
				'here_link',
				$k,
				$ms[0][$k],
				trim($m)
			);
		}
		static::addErrorToHtml($url, 'here_link', static::$error_ids[$url], 'ignores');
	}
}
