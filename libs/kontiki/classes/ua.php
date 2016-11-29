<?php
namespace Kontiki;
/**
 * Lazy UA detection
 * thx https://gist.github.com/takahashi-yuki/4667353
 * @version 1.2.2
 */
class Ua
{
	protected static $ua;
	protected static $accept;

	/**
	 * _init
	 *
	 * @return  void
	 */
	public static function _init()
	{
		static::$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		static::$accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
	}

	/**
	 * get IE version by numeric
	 * IE: <=1
	 * none IE: 0
	 * @return int
	 */
	public static function get_ie_version()
	{
		if (stristr(static::$ua, "MSIE"))
		{
			preg_match('/MSIE\s([\d.]+)/i', static::$ua, $ver);
			$ver = @floor($ver[1]);
		}
		elseif (stristr(static::$ua, "Trident"))
		{
			preg_match('/rv\:([\d.]+)/i', static::$ua, $ver);
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

		if (isset(static::$ua))
		{
			if (self::getIEVersion() >= 10)
			{
				$type = 'modern';
			}
			else if (strpos(static::$ua, 'Mobile') !== false
			       || strpos(static::$ua, 'Android') !== false
			       || strpos(static::$ua, 'Silk/') !== false
			       || strpos(static::$ua, 'Kindle') !== false
			       || strpos(static::$ua, 'BlackBerry') !== false
			       || strpos(static::$ua, 'Opera Mini') !== false
			       || strpos(static::$ua, 'Opera Mobi') !== false)
			{
				$type = 'mobile';
			}
			else if (strpos(static::$ua, 'bot') !== false
			       || strpos(static::$ua, 'spider') !== false
			       || strpos(static::$ua, 'archiver') !== false
			       || strpos(static::$ua, 'Google') !== false
			       || strpos(static::$ua, 'Yahoo') !== false)
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
