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

	/**
	 * fetch html
	 * JUST fetch. NOT save. But Reusable.
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @return String|Bool
	 */
	public static function fetchHtml($url, $ua = 'using', $type = 'raw')
	{
		$ua = $ua == 'using' ? Input::userAgent() : $ua;
		if ( ! is_string($ua)) Util::error();

		if (isset(static::$htmls[$url][$ua][$type]))
		{
			return static::$htmls[$url][$ua][$type];
		}

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
			static::$htmls[$url][$ua][$type] = false;
			return false;
		}

		// normally fetch by UTF-8
		if (mb_detect_encoding($bool_or_html) == 'UTF-8')
		{
			static::$htmls[$url][$ua][$type] = $bool_or_html;
			return $bool_or_html;
		}

		// not UTF-8...
		$charset = self::recognitionCharset($bool_or_html);
		$bool_or_html = mb_convert_encoding($bool_or_html, 'UTF-8', $charset);

		static::$htmls[$url][$ua][$type] = $bool_or_html;

		return $bool_or_html;
	}

	/**
	 * set html
	 *
	 * @param  String $html
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
//		$charset = empty($charset) ? 'Shift_JIS' : $charset;
		return $charset;
	}

	/**
	 * set html
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @param  String|Bool $html
	 * @param  String $type ['raw', 'high-lighted', 'ignored']
	 * @return Void
	 */
	public static function addHtml($url, $ua = 'using', $html = '', $type = 'raw')
	{
		if ( ! is_string($html)) Util::error('invalid HTML was given');

		// insert
		$types = array(
			'raw',
			'high-lighted',
			'ignored',
		);

		if ($type != 'high-lighted') unset($types[1]);

		foreach ($types as $each_type)
		{
			if ($type && $each_type != $type) continue;

			// delete
			$sql = 'DELETE FROM '.A11YC_TABLE_CACHES.' WHERE ';
			$sql.= '`url` = ? AND `ua` = ? AND `type` = ?;';
			Db::execute($sql, array($url, $ua, $each_type));

			// type
			if ($each_type == 'ignored')
			{
				$html = Element\Get::ignoredHtml($html);
			}

			// sql
			$sql = 'INSERT INTO '.A11YC_TABLE_CACHES;
			$sql.= ' (`url`, `ua`, `type`, `data`, `updated_at`) VALUES';
			$sql.= ' (?, ?, ?, ?, ?);';
			Db::execute($sql, array($url, $ua, $each_type, $html, date('Y-m-d H:i:s')));
		}
	}

	/**
	 * get html
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @param  String $type ['raw', 'high-lighted', 'ignored']
	 * @param  Bool $force
	 * @return String|Bool
	 */
	public static function getHtml($url, $ua = 'using', $type = 'raw', $force = false)
	{
		$url = Util::urldec($url);
		$ua = empty($ua) ? 'using' : $ua;

		if (isset(static::$htmls[$url][$ua][$type]) && $force === false)
		{
			return static::$htmls[$url][$ua][$type];
		}

		// check cache
		$sql = 'SELECT `data`, `updated_at` FROM '.A11YC_TABLE_CACHES.' WHERE ';
		$sql.= '`url` = ? AND `type` = ?;';
		$result = Db::fetch($sql, array($url, $type));

		if ($result['updated_at'] < date('Y-m-d H:i:s', time() - 86400))
		{
			// fetch from internet
			$html = self::fetchHtml($url, $ua, $type);
		}

		if (
			($result['data'] && $force === false) ||
			($result['data'] && $html === false)
		)
		{
			static::$htmls[$url][$ua][$type] = $result['data'];
		}
		else
		{
			// add and fetch
			if ($html === false) return false;
			self::addHtml($url, $ua, $html, $type);
			$sql = 'SELECT `data` FROM '.A11YC_TABLE_CACHES.' WHERE ';
			$sql.= '`url` = ? AND `type` = ?;';
			$result = Db::fetch($sql, array($url, $type));
			static::$htmls[$url][$ua][$type] = $result['data'];
		}

		return static::$htmls[$url][$ua][$type];
	}

	/**
	 * fetch page title
	 *
	 * @param  String $url
	 * @param  Bool $is_from_web
	 * @return String
	 */
	public static function fetchPageTitle($url, $is_from_web = false)
	{
		if (isset(static::$titles[$url])) return static::$titles[$url];
		$html = self::getHtml($url, 'using', 'raw', $is_from_web);
		if ($html === false) return '';

		static::$titles[$url] = self::fetchPageTitleFromHtml($html);
		return static::$titles[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param  String|Bool $html
	 * @return String
	 */
	public static function fetchPageTitleFromHtml($html)
	{
		if ( ! is_string($html)) Util::error('invalid HTML was given');
		preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);
		return $title;
	}
}
