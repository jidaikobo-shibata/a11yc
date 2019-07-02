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
	 * fetch raw
	 *
	 * @param String $url
	 * @param String $ua
	 * @param Bool $force
	 * @return String|Bool
	 */
	public static function fetchRaw($url, $ua = 'using', $force = false)
	{
		$url = Util::urldec($url);
		$ua = empty($ua) ? 'using' : $ua;

		if (isset(static::$htmls[$url][$ua]) && $force === false) return static::$htmls[$url][$ua];
		static::$htmls[$url][$ua] = Data::fetchArr('html', $url, array());
		return static::$htmls[$url][$ua];
	}

	/**
	 * fetch
	 *
	 * @param String $url
	 * @param String $ua
	 * @param Bool $force
	 * @return String|Bool
	 */
	public static function fetch($url, $ua = 'using', $force = false)
	{
		$vals = static::fetchRaw($url, $ua, $force);
		$html = Arr::get($vals, $ua, false);
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
	 * @param String|Bool $data
	 * @param String $ua
	 * @return Bool
	 */
	public static function insert($url, $data = '', $ua = 'using')
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
	 * page title
	 *
	 * @param String $url
	 * @return String
	 */
	public static function pageTitle($url)
	{
		if (isset(static::$titles[$url])) return static::$titles[$url];

		$html = static::fetch($url);
		$page = Page::fetch($url);
		$title_from_db = Arr::get($page, 'title', '');

		$title = empty($title_from_db) ? static::pageTitleFromHtml($html) : $title_from_db;
		static::$titles[$url] = $title;

		return static::$titles[$url];
	}

	/**
	 * page title from html
	 *
	 * @param String|Bool $html
	 * @return String
	 */
	public static function pageTitleFromHtml($html)
	{
		if ( ! is_string($html)) return '';
		preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);

		return $title;
	}

	/**
	 * last update
	 *
	 * @param String $url
	 * @return String
	 */
	public static function lastUpdate($url)
	{
		$url = Util::urldec($url);
		$cache = Data::fetchArr('html', $url, array());
		$updated_at = Arr::get($cache, 'updated_at', 0);
		return $updated_at;
	}
}
