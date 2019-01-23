<?php
/**
 * * A11yc\Validate\Check\CssContent
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
use A11yc\Model;

class CssContent extends Validate
{
	/**
	 * check content
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'css_is_meanfull_content', self::$unspec, 5);
		if ( ! static::$do_css_check) return;
		Validate\Set::log($url, 'css_is_meanfull_content', self::$unspec, 1);

		$csses = static::css($url);
		if (empty($csses))
		{
			Validate\Set::log($url, 'css_is_meanfull_content', self::$unspec, 4);
			return;
		}

		$is_exists = false;
		$k = 0;
		foreach ($csses as $each_csses)
		{
			foreach ($each_csses as $selector => $props)
			{
				if ( ! isset($props['content'])) continue;
				$is_exists = true;
				if (
					in_array(
						$props['content'],
						array("''", '""', '.', 'none', '"."', "'.'", '" "', "' '")
					)
				)
				{
					continue; // clearfix or none
				}
				if (in_array(substr($props['content'], 0, 2), array("'\\", '"\\'))) continue; // decoration
				$tstr = $selector.': '.Util::s($props['content']);
				Validate\Set::error($url, 'css_is_meanfull_content', $k, '', $tstr);
				$k++;
			}
		}

		if ( ! $is_exists)
		{
			Validate\Set::log($url, 'css_is_meanfull_content', self::$unspec, 4);
		}

	}
}
