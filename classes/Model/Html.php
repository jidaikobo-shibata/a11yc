<?php
/**
 * A11yc\Model\Html
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

use A11yc\Element;

class Html
{
	protected static $htmls = array();
	protected static $titles = array();

	public static $fields = array(
		'ua' => 'using',
		'data' => '',
		'updated_at' => ''
	);

	/**
	 * fetch
	 *
	 * @param String $url
	 * @param String $ua
	 * @param Bool $force
	 * @param Bool $is_use_cache
	 * @return String|Bool
	 */
	public static function fetch($url, $ua = 'using', $force = false, $is_use_cache = true)
	{
		$url = Util::urldec($url);
		$ua = empty($ua) ? 'using' : $ua;

		if (isset(static::$htmls[$url][$ua]) && $force === false)
		{
			return static::$htmls[$url][$ua];
		}

		// check db cache and internet
		$html = self::cacheOrInternet($url, $ua, $is_use_cache);

		// 65535 is sqlite filed limit
		if (strlen($html) <= 65530 && $is_use_cache === false)
		{
			static::insert($url, $ua, $html);
		}

		static::$htmls[$url][$ua] = $html;
		return static::$htmls[$url][$ua];
	}

	/**
	 * use cache or fetch from Internet
	 *
	 * @param String $url
	 * @param String $ua
	 * @param Bool $is_use_cache
	 * @return String
	 */
	private static function cacheOrInternet($url, $ua, $is_use_cache)
	{
		$cache      = Data::fetch('html', $url, array());
		$updated_at = Arr::get($cache, 'updated_at', 0);
		$html       = Arr::get($cache, $ua, false);
		$cache_time = intval(Setting::fetch('cache_time', 60));
		$cache_time = $is_use_cache ? $cache_time : -1;

		// do not use internet
		if ($cache_time == -1) return $html;

		// fetch from internet
		if (
			strtotime($updated_at) < time() - $cache_time ||
			strlen($html) >= 65530 // maybe broken, too long for sqlite
		)
		{
			$html = self::fetchHtmlFromInternet($url, $ua);
		}

		return $html;
	}

	/**
	 * fetch html from internet
	 * JUST fetch. NOT save. But Reusable.
	 *
	 * @param String $url
	 * @param String $ua
	 * @return String|Bool
	 */
	public static function fetchHtmlFromInternet($url, $ua = 'using')
	{
		$ua = $ua == 'using' ? Input::userAgent() : $ua;
		if ( ! is_string($ua)) Util::error();
		if (isset(static::$htmls[$url][$ua])) return static::$htmls[$url][$ua];

		Guzzle::forge($url);
		Guzzle::instance($url)
			->set_config(
				'User-Agent',
				Util::s($ua.' GuzzleHttp/a11yc (+http://www.jidaikobo.com)')
			);
		$bool_or_html = Guzzle::instance($url)->is_html ?
									Guzzle::instance($url)->body :
									false;

		// failed to fetch
		if ( ! $bool_or_html)
		{
			static::$htmls[$url][$ua] = false;
			return false;
		}

		// normally fetch by UTF-8
		if (mb_detect_encoding($bool_or_html) == 'UTF-8')
		{
			static::$htmls[$url][$ua] = $bool_or_html;
			return $bool_or_html;
		}

		// not UTF-8...
		$charset = self::recognitionCharset($bool_or_html);
		$bool_or_html = mb_convert_encoding($bool_or_html, 'UTF-8', $charset);

		static::$htmls[$url][$ua] = $bool_or_html;

		return $bool_or_html;
	}

	/**
	 * recognition Charset
	 *
	 * @param String $html
	 * @return String
	 */
	private static function recognitionCharset($html)
	{
		$str = Element::ignoreElementsByStr($html);
		// Do not use Element\Get::elementsByRe() because crashed character cause bad cache
		preg_match_all("/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i", $str, $ms);

		$charset = '';
		foreach ($ms[1] as $k => $v)
		{
			if (strtolower($v) == 'meta')
			{
				$attrs = Element\Get::attributes($ms[0][$k]);
				if ($charset = Arr::get($attrs, 'charset')) break;
				if (isset($attrs['http-equiv']) && strtolower($attrs['http-equiv']) == 'content-type')
				{
					preg_match('/charset=(.+)/i', $attrs['content'], $mms);
					if (isset($mms[1]))
					{
						$charset = $mms[1];
						break;
					}
				}
			}
		}

		return $charset ?: "JIS, eucjp-win, sjis-win";
	}

	/**
	 * insert
	 *
	 * @param String $url
	 * @param String $ua
	 * @param String|Bool $data
	 * @return Void
	 */
	public static function insert($url, $ua = 'using', $data = '')
	{
		if (empty($url)) return false;
		$url = Util::urldec($url);

		// delete
		Data::delete('html', $url);

		$vals = array();
		$ua = empty($ua) ? Arr::get(static::$fields, 'ua') : $ua;
		$vals[$ua] = empty($data) ? Arr::get(static::$fields, 'data') : $data;
		$vals['updated_at'] = date('Y-m-d H:i:s');

		return Data::insert('html', $url, $vals);
	}

	/**
	 * fetch page title
	 *
	 * @param String $url
	 * @param Bool $is_from_web
	 * @return String
	 */
	public static function fetchPageTitle($url, $is_from_web = false)
	{
		if (isset(static::$titles[$url])) return static::$titles[$url];
		$html = self::fetch($url, 'using', $is_from_web);
		$page = Page::fetch($url);
		$title_from_db = Arr::get($page, 'title', '');

		$title = empty($title_from_db) ? self::fetchPageTitleFromHtml($html) : $title_from_db;
		static::$titles[$url] = $title;

		return static::$titles[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param String|Bool $html
	 * @return String
	 */
	public static function fetchPageTitleFromHtml($html)
	{
		if ( ! is_string($html)) return '';
		preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);
		return $title;
	}
}
