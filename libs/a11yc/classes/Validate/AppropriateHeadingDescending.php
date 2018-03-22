<?php
/**
 * A11yc\Validate\AppropriateHeadingDescending
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

class AppropriateHeadingDescending extends Validate
{
	/**
	 * appropriate heading descending
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		$str = Element::ignoreElements(static::$hl_htmls[$url]);

		$secs = preg_split("/\<(h[^\>?]+?)\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		if ( ! $secs[0]) return;

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

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? $secs[$k + 1] : $v[1];

				static::$error_ids[$url]['appropriate_heading_descending'][$k]['id'] = '<'.$v.'>'.$str;
				static::$error_ids[$url]['appropriate_heading_descending'][$k]['str'] = $str;
			}
			$prev = $current_level;
		}
		static::addErrorToHtml($url, 'appropriate_heading_descending', static::$error_ids[$url], 'ignores');
	}
}
