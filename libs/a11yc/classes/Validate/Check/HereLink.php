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

class HereLink extends Validate
{
	/**
	 * here link
	 *
	 * @return  void
	 */
	public static function check($url)
	{
		static::setLog($url, 'here_link', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);
		$ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values');
		if ( ! $ms[2])
		{
			static::setLog($url, 'here_link', self::$unspec, 4);
			return;
		}

		$heres = array_map('trim', explode(',', A11YC_LANG_HERE));
		foreach ($ms[2] as $k => $m)
		{
			$tstr = $ms[0][$k];
			$m = trim($m);
			if (in_array(strtolower($m), $heres))
			{
				static::setError($url, 'here_link', $k, $tstr, $tstr);
			}
			else
			{
				static::setLog($url, 'here_link', $tstr, 2);
			}
		}
		static::addErrorToHtml($url, 'here_link', static::$error_ids[$url], 'ignores');
	}
}
