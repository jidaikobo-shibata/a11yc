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
		if (stristr(@$_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			preg_match('/MSIE\s([\d.]+)/i', @$_SERVER['HTTP_USER_AGENT'], $ver);
			$ver = @floor($ver[1]);
		}
		elseif (stristr(@$_SERVER['HTTP_USER_AGENT'], "Trident"))
		{
			preg_match('/rv\:([\d.]+)/i', @$_SERVER['HTTP_USER_AGENT'], $ver);
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

		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			if (self::getIEVersion() >= 10)
			{
				$type = 'modern';
			}
			else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false)
			{
				$type = 'mobile';
			}
			else if (strpos($_SERVER['HTTP_USER_AGENT'], 'bot') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'spider') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'archiver') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Google') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo') !== false)
			{
				$type = 'robot';
			}
			else if (isset($_SERVER['HTTP_ACCEPT']))
			{
				if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false
						|| strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false)
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
