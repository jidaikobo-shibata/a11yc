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

class Html
{
	protected static $htmls = array();
	protected static $titles = array();

	public static $ignores = array(
		"/\<script.+?\<\/script\>/si",
		"/\<style.+?\<\/style\>/si",
		"/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
	);

	public static $ignores_comment_out = array(
		"/\<!--.+?--\>/si",
	);

	/**
	 * fetch html
	 * JUST fetch. NOT save. But Reusable.
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @return String
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
		static::$htmls[$url][$ua][$type] = Guzzle::instance($url)->is_html ?
																			Guzzle::instance($url)->body :
																			false;

		return static::$htmls[$url][$ua][$type];
	}

	/**
	 * set html
	 *
	 * @param  String $url
	 * @param  String $ua
	 * @param  String $html
	 * @param  String $type ['raw', 'high-lighted', 'ignored', 'ignored_comments']
	 * @return Void
	 */
	public static function addHtml($url, $ua = 'using', $html = '', $type = 'raw')
	{
		// insert
		$types = array(
			'raw',
			'high-lighted',
			'ignored',
			'ignored_comments'
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
				$html = self::ignoreElements($html);
			}
			elseif ($each_type == 'ignored_comments')
			{
				$html = self::ignoreCommentOut($html);
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
	 * @param  String $type ['raw', 'high-lighted', 'ignored', 'ignored_comments']
	 * @param  String $force
	 * @return String|Bool
	 */
	public static function getHtml($url, $ua = 'using', $type = 'raw', $force = false)
	{
		$url = Util::urldec($url);
		$ua = empty($ua) ? 'using' : $ua;

		if (isset(static::$htmls[$url][$ua][$type]) && ! $force)
		{
			return static::$htmls[$url][$ua][$type];
		}

		// check cache
		$sql = 'SELECT `data` FROM '.A11YC_TABLE_CACHES.' WHERE ';
		$sql.= '`url` = ? AND `type` = ? AND `updated_at` > ?;';
		$result = Db::fetch($sql, array($url, $type, date('Y-m-d H:i:s', time() - 86400)));

		if ($result['data'] && ! $force)
		{
			static::$htmls[$url][$ua][$type] = $result['data'];
		}
		else
		{
			// fetch from internet
			$html = self::fetchHtml($url, $ua, $type);

			// add and fetch
			self::addHtml($url, $ua, $html, $type);
			$sql = 'SELECT `data` FROM '.A11YC_TABLE_CACHES.' WHERE ';
			$sql.= '`url` = ? AND `type` = ?;';
			$result = Db::fetch($sql, array($url, $type));
			static::$htmls[$url][$ua][$type] = $result['data'];
		}

		return static::$htmls[$url][$ua][$type];
	}

	/**
	 * ignore elements
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function ignoreElements($str)
	{
		// ignore comment out, script, style
		$ignores = array_merge(static::$ignores, static::$ignores_comment_out);

		foreach ($ignores as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}

		// set first tag
		// $first_tags = Element::getElementsByRe($str, 'ignores', 'tags');
		// static::$first_tag = Arr::get($first_tags, '0.0');

		return $str;
	}

	/**
	 * ignoreCommentOut
	 *
	 * @param  String $str
	 * @return String
	 */
	public static function ignoreCommentOut($str)
	{
		static $retval = '';
		if ($retval) return $retval;

		// ignore comment out only
		foreach (static::$ignores_comment_out as $ignore)
		{
			$str = preg_replace($ignore, '', $str);
		}
		$retval = $str;
		return $retval;
	}

	/**
	 * fetch page title
	 *
	 * @param  String $url
	 * @param  String $is_from_web
	 * @return String
	 */
	public static function fetchPageTitle($url, $is_from_web = false)
	{
		if (isset(static::$titles[$url])) return static::$titles[$url];
		$html = self::getHtml($url, 'using', 'raw', $is_from_web);
		if ( ! $html) return '';

		static::$titles[$url] = self::fetchPageTitleFromHtml($html);
		return static::$titles[$url];
	}

	/**
	 * fetch page title from html
	 *
	 * @param  String $html
	 * @return String
	 */
	public static function fetchPageTitleFromHtml($html)
	{
		preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
		$tmp = isset($m[1]) ? $m[1] : '';
		$title = str_replace(array("\n", "\r"), '', $tmp);
		return $title;
	}
}
