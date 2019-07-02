<?php
/**
 * A11yc\Model\Css
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

use A11yc\Element;

class Css
{
	use CssFormat;

	protected static $csses = array();
	public static $is_suspicious_paren_num = false;
	public static $suspicious_prop_and_vals = array();
	public static $suspicious_props = array();
	public static $suspicious_val_prop = array();

	/**
	 * fetch css
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function fetchCss($url, $ua = 'using')
	{
		$ua = $ua == 'using' ? Input::userAgent() : $ua;
		if ( ! is_string($ua)) Util::error();
		if (isset(static::$csses[$url][$ua])) return static::$csses[$url][$ua];

		$html = Html::fetchHtmlFromInternet($url, $url);
		if ($html === false) return array();
		$css = self::getConvinedCssFromHtml($html, $ua);
		static::$csses[$url][$ua] = CssFormat::makeArray($css);

		return static::$csses[$url][$ua];
	}

	/**
	 * get css from html
	 *
	 * @param String|Bool $html
	 * @param String $ua
	 * @return String
	 */
	private static function getConvinedCssFromHtml($html, $ua)
	{
		if ( ! is_string($html)) Util::error('invalid HTML was given');

		$css = '';

		// style tag
		if (preg_match_all("/\<style[^\>]*\>(.*?)\<\/style\>/si", $html, $ms))
		{
			foreach ($ms[1] as $m)
			{
				$css.= $m;
			}
		}

		// file
		$css.= self::getCssFileFromLink($html, $ua);

		return $css;
	}

	/**
	 * get css from link
	 *
	 * @param String|Bool $html
	 * @param String $ua
	 * @return String
	 */
	private static function getCssFileFromLink($html, $ua)
	{
		$css = '';
		$ua = $ua == 'using' ? Input::userAgent() : $ua;
		if (preg_match_all("/\<link [^\>]*\>/si", $html, $ms))
		{
			foreach ($ms[0] as $m)
			{
				if (strpos($m, 'stylesheet') === false) continue;
				$attrs = Element\Get::attributes($m);
				if ( ! isset($attrs['href'])) continue;
				$url = $attrs['href'];
				$url = self::enuniqueUri($url);
				if ( ! Crawl::isPageExist($url)) continue;
				$current_css = self::fetchFromInternet($url, $ua);
				$css.= $current_css;

				// @import
				if (preg_match_all("/@import *?url *?\((.+?)\)/si", $current_css, $mms))
				{
					foreach ($mms[1] as $import_url)
					{
						$import_url = trim($import_url, '"');
						$import_url = trim($import_url, "'");
						$import_url = self::enuniqueUri($import_url, $url);
						$css.= self::fetchFromInternet($import_url, $ua);
					}
				}
			}
		}
		return $css;
	}

	/**
	 * fetch from internet
	 *
	 * @param String $url
	 * @param String $ua
	 * @return String
	 */
	private static function fetchFromInternet($url, $ua = 'using')
	{
		Guzzle::forge($url);
		Guzzle::instance($url)
			->set_config(
				'User-Agent',
				Util::s($ua.' GuzzleHttp/a11yc (+http://www.jidaikobo.com)')
			);
		return Guzzle::instance($url)->body;
	}

	/**
	 * enuniqueUri
	 *
	 * @param String $url
	 * @param String $css_url
	 * @return String
	 */
	private static function enuniqueUri($url, $css_url = '')
	{
		if (strpos($url, 'http') === 0)
		{
			return $url;
		}

		if (strlen($url) >= 2 && $url[0] == '/' && $url[1] != '/')
		{
			return Util::enuniqueUri($url);
		}

		return self::enuniqueCssUri($url, $css_url);
	}

	/**
	 * enuniqueCssUri
	 *
	 * @param String $url
	 * @param String $css_url
	 * @return String
	 */
	private static function enuniqueCssUri($url, $css_url = '')
	{
		if ($css_url)
		{
			$css_urls = explode('/', $css_url);
			array_pop($css_urls);
			$css_url = join('/', $css_urls);

			// started with "./"
			if (strlen($url) >= 2 && $url[0] == '.' && $url[1] == '/')
			{
				$url = $css_url.substr($url, 1);
			}
			// started with "../"
			elseif (strlen($url) >= 3 && $url[0] == '.' && $url[1] == '.' && $url[2] == '/')
			{
				$strs = explode('../', $url);
				$upper_num = count($strs) - 1;
				for ($n = 1; $n <= $upper_num; $n++)
				{
					array_pop($css_urls);
				}
				$css_url = join('/', $css_urls);
				$url = $css_url.'/'.end($strs);
			}
			elseif (strpos($url, 'http') !== 0)
			{
				$url = $css_url.'/'.$url;
			}
		}

		return Util::urldec($url);
	}
}
