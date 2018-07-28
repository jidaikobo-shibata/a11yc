<?php
/**
 * Kontiki\Ua
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 * @thx https://gist.github.com/takahashi-yuki/4667353
 */
namespace Kontiki;

class Ua
{
	/**
	 * get IE version by numeric
	 * IE: <=1
	 * none IE: 0
	 * @return Integer
	 */
	public static function getIEVersion()
	{
		if (stristr(Input::userAgent(), "MSIE"))
		{
			preg_match('/MSIE\s([\d.]+)/i', Input::userAgent(), $ver);
			$ver = @floor($ver[1]);
		}
		elseif (stristr(Input::userAgent(), "Trident"))
		{
			preg_match('/rv\:([\d.]+)/i', Input::userAgent(), $ver);
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
	 * @return String
	 * @link http://developer.wordpress.org/reference/functions/wp_is_mobile/
	 */
	public static function getBrowserType()
	{
		$type = 'legacy';

		if (Input::userAgent())
		{
			if (self::getIEVersion() >= 10)
			{
				$type = 'modern';
			}
			else if (
				strpos(Input::userAgent(), 'Mobile') !== false ||
				strpos(Input::userAgent(), 'Android') !== false ||
				strpos(Input::userAgent(), 'Silk/') !== false ||
				strpos(Input::userAgent(), 'Kindle') !== false ||
				strpos(Input::userAgent(), 'BlackBerry') !== false ||
				strpos(Input::userAgent(), 'Opera Mini') !== false ||
				strpos(Input::userAgent(), 'Opera Mobi') !== false
			)
			{
				$type = 'mobile';
			}
			else if (
				strpos(Input::userAgent(), 'bot') !== false ||
				strpos(Input::userAgent(), 'spider') !== false ||
				strpos(Input::userAgent(), 'archiver') !== false ||
				strpos(Input::userAgent(), 'Google') !== false ||
				strpos(Input::userAgent(), 'Yahoo') !== false
			)
			{
				$type = 'robot';
			}
			else if (isset(static::$accept))
			{
				if (
					strpos(static::$accept, 'application/xml') !== false ||
					strpos(static::$accept, 'application/xhtml+xml') !== false
				)
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
	 * @return Bool
	 */
	public static function isModernBrowser()
	{
		return (bool) (self::getBrowserType() === 'modern');
	}

	/**
	 * is legacy browser
	 *
	 * @return Bool
	 */
	public static function isLegacyBrowser()
	{
		return (bool) (self::getBrowserType() === 'legacy');
	}

	/**
	 * is mobile (smart phone or tablet)
	 *
	 * @return Bool
	 */
	public static function isMobile()
	{
		return (bool) (self::getBrowserType() === 'mobile');
	}

	/**
	 * is bot
	 *
	 * @return Bool
	 */
	public static function isBot()
	{
		return (bool) (self::getBrowserType() === 'robot');
	}
}
