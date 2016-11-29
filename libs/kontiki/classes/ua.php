<?php
namespace Kontiki;
/**
 * Lazy UA detection
 * thx https://gist.github.com/takahashi-yuki/4667353
 * @version 1.2.2
 */
class Ua
{
	/**
	 * get IE version by numeric
	 * IE: <=1
	 * none IE: 0
	 * @return int
	 */
	public static function get_ie_version()
	{
		if (stristr(Input::user_agent(), "MSIE"))
		{
			preg_match('/MSIE\s([\d.]+)/i', Input::user_agent(), $ver);
			$ver = @floor($ver[1]);
		}
		elseif (stristr(Input::user_agent(), "Trident"))
		{
			preg_match('/rv\:([\d.]+)/i', Input::user_agent(), $ver);
			$ver = $ver[1];
		}
		else
		{
			$ver = 0;
		}
		return (int) $ver;
	}

	/**
	 * get browser type
	 *
	 * @return string
	 * @link http://developer.wordpress.org/reference/functions/wp_is_mobile/
	 */
	public static function get_browser_type()
	{
		$type = 'legacy';

		if (Input::user_agent())
		{
			if (self::getIEVersion() >= 10)
			{
				$type = 'modern';
			}
			else if (strpos(Input::user_agent(), 'Mobile') !== false
			       || strpos(Input::user_agent(), 'Android') !== false
			       || strpos(Input::user_agent(), 'Silk/') !== false
			       || strpos(Input::user_agent(), 'Kindle') !== false
			       || strpos(Input::user_agent(), 'BlackBerry') !== false
			       || strpos(Input::user_agent(), 'Opera Mini') !== false
			       || strpos(Input::user_agent(), 'Opera Mobi') !== false)
			{
				$type = 'mobile';
			}
			else if (strpos(Input::user_agent(), 'bot') !== false
			       || strpos(Input::user_agent(), 'spider') !== false
			       || strpos(Input::user_agent(), 'archiver') !== false
			       || strpos(Input::user_agent(), 'Google') !== false
			       || strpos(Input::user_agent(), 'Yahoo') !== false)
			{
				$type = 'robot';
			}
			else if (isset(static::$accept))
			{
				if (strpos(static::$accept, 'application/xml') !== false
						|| strpos(static::$accept, 'application/xhtml+xml') !== false)
				{
					$type = 'modern';
				}
			}
		}

		return (string) $type;
	}

	/**
	 * is modern browser
	 *
	 * @return bool
	 */
	public static function is_modern_browser()
	{
		return (bool) (self::getBrowserType() === 'modern');
	}

	/**
	 * is legacy browser
	 *
	 * @return bool
	 */
	public static function is_legacy_browser()
	{
		return (bool) (self::getBrowserType() === 'legacy');
	}

	/**
	 * is mobile (smart phone or tablet)
	 *
	 * @return bool
	 */
	public static function is_mobile()
	{
		return (bool) (self::getBrowserType() === 'mobile');
	}

	/**
	 * is bot
	 *
	 * @return bool
	 */
	public static function is_bot()
	{
		return (bool) (self::getBrowserType() === 'robot');
	}
}
