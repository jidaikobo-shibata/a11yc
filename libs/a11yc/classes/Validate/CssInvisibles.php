<?php
/**
 * A11yc\Validate\CssInvisibles
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class CssInvisibles extends Validate
{
	/**
	 * check content
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		if ( ! static::$do_css_check) return;

		$csses = static::css($url);
		if ( ! $csses) return;

		$k = 0;
		foreach ($csses as $each_csses)
		{
			foreach ($each_csses as $selector => $props)
			{
				// display, visibility
				if (
					(isset($props['display']) && $props['display'] == 'none') ||
					(isset($props['visibility']) && $props['visibility'] == 'hidden')
				)
				{
					static::$error_ids[$url]['css_invisible'][$k]['id'] = '';
					static::$error_ids[$url]['css_invisible'][$k]['str'] = $selector;
				}

				// background-image without background-color
				if (
					isset($props['background']) ||
					isset($props['background-image'])
				)
				{
					$background = Arr::get($props, 'background', '');
					$background_image = Arr::get($props, 'background-image', '');

					if (
						strpos($background, 'url') !== false ||
						strpos($background_image, 'url') !== false
					)
					{
						if (
							strpos($background, '#') === false &&
							! isset($props['background-color'])
						)
						{
							static::$error_ids[$url]['css_background_image_only'][$k]['id'] = '';
							static::$error_ids[$url]['css_background_image_only'][$k]['str'] = $selector;
						}
					}
				}
				$k++;
			}
		}
	}
}
