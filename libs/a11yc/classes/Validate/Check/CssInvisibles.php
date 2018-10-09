<?php
/**
 * * A11yc\Validate\Check\CssInvisibles
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
		Validate\Set::log($url, 'css_invisible', self::$unspec, 5);
		Validate\Set::log($url, 'css_background_image_only', self::$unspec, 5);
		if ( ! static::$do_css_check) return;
		Validate\Set::log($url, 'css_invisible', self::$unspec, 1);
		Validate\Set::log($url, 'css_background_image_only', self::$unspec, 1);

		$csses = static::css($url);
		if ( ! $csses)
		{
			Validate\Set::log($url, 'css_invisible', self::$unspec, 4);
			Validate\Set::log($url, 'css_background_image_only', self::$unspec, 4);
			return;
		}

		$is_exists_visible = false;
		$is_exists_bg = false;

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
					$is_exists_visible = true;
					Validate\Set::error($url, 'css_invisible', $k, '', $selector);
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
						$is_exists_bg = true;
						if (
							strpos($background, '#') === false &&
							! isset($props['background-color'])
						)
						{
							Validate\Set::error($url, 'css_background_image_only', $k, '', $selector);
						}
					}
				}
				$k++;
			}
		}

		if ( ! $is_exists_visible)
		{
			Validate\Set::log($url, 'css_invisible', self::$unspec, 4);
		}

		if ( ! $is_exists_bg)
		{
			Validate\Set::log($url, 'css_background_image_only', self::$unspec, 4);
		}

	}
}
