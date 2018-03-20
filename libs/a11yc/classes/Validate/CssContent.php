<?php
/**
 * A11yc\Validate\CssContent
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class CssContent extends Validate
{
	/**
	 * check content
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if ( ! static::doCssCheck()) return;

		$csses = static::css($url);
		if ( ! $csses) return;

		$k = 0;
		foreach ($csses as $type => $each_csses)
		{
			foreach ($each_csses as $selector => $props)
			{
				if ( ! isset($props['content'])) continue;
				if (
					in_array(
						$props['content'],
						array("''", '""', '.', 'none', '"."', "'.'", '" "', "' '")
					)
				) continue; // clearfix or none
				if (in_array(substr($props['content'], 0, 2), array("'\\", '"\\'))) continue; // decoration
				static::$error_ids[$url]['css_is_meanfull_content'][$k]['id'] = '';
				static::$error_ids[$url]['css_is_meanfull_content'][$k]['str'] = $selector.': '.Util::s($props['content']);
				$k++;
			}
		}
	}
}
