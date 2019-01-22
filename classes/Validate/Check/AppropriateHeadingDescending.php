<?php
/**
 * * A11yc\Validate\Check\AppropriateHeadingDescending
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

class AppropriateHeadingDescending extends Validate
{
	/**
	 * appropriate heading descending
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'appropriate_heading_descending', self::$unspec, 1);
		$str = Element\Get::ignoredHtml($url);

		$secs = preg_split("/\<(h[^\>?]+?)\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		if ( ! $secs[0])
		{
			Validate\Set::log($url, 'appropriate_heading_descending', self::$unspec, 4);
			return;
		}

		// get first appeared heading
		$prev = 1;
		foreach ($secs as $sec)
		{
			if (isset($sec[1]) && is_numeric($sec[1]))
			{
				$prev = $sec[1];
				break;
			}
		}

		foreach ($secs as $k => $v)
		{
			if ($v[0] != 'h' || ! is_numeric($v[1])) continue; // skip non heading
			$current_level = $v[1];
			$tstr = '<'.$v.'>';

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? $secs[$k + 1] : $v[1];
				Validate\Set::error($url, 'appropriate_heading_descending', $k, $tstr, $str);
			}
			else
			{
				Validate\Set::log($url, 'appropriate_heading_descending', $tstr, 2);
			}
			$prev = $current_level;
		}

		static::addErrorToHtml($url, 'appropriate_heading_descending', static::$error_ids[$url], 'ignores');
	}
}
