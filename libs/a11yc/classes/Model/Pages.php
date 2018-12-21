<?php
/**
 * A11yc\Model\Pages
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Pages
{
	protected static $loaded_pages = array();

	/**
	 * fetch
	 *
	 * @param Bool $args
	 * @return Array
	 */
	public static function fetch($args = array())
	{
		$list         = Arr::get($args,   'list', 'all');
		$reason       = Arr::get($args,   'reason', null);
		$type         = Arr::get($args,   'type', null);
		$is_current   = Arr::get($args,   'is_current', true);
		$words        = Arr::get($args,   'words', array());
		$words        = is_array($words) ? $words : array();
		$orderby      = Arr::get($args,   'order', 'seq_asc');
		$placeholders = array();

		// whrs
		$url_sql = ' `url` <> "" AND ';
		$whrs = array(
			'all'   => $url_sql.'`trash` = 0 ',
			'yet'   => $url_sql.'`done` = 0 AND `trash` = 0 ',
			'done'  => $url_sql.'`done` = 1 and `trash` = 0 ',
			'trash' => $url_sql.'`trash` = 1 ',
		);
		$list = is_string($list) && array_key_exists($list, $whrs) ? $list : 'all';

		// selection_reason
		list($sql_reason, $placeholders_reason) = self::setReason($reason);
		$placeholders = array_merge($placeholders, $placeholders_reason);

		// type - html or pdf?
		list($sql_type, $placeholders_types) = self::setType($type);
		$placeholders = array_merge($placeholders, $placeholders_types);

		// pages
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE '.$whrs[$list];

		// version
		$sql.= $is_current ? Db::currentVersionSql() : Db::versionSql();

		// search
		list($sql_like, $placeholders_likes) = self::setSearch($words);
		$placeholders = array_merge($placeholders, $placeholders_likes);

		// order
		$sql_odr = self::setOrder($orderby);

		// fetch pages
		if ( ! empty($placeholders))
		{
			$whr = '';
			$whr.= $sql_reason ?: '';
			$whr.= $sql_type ?: '';
			$whr.= $sql_like ?: '';
			$pages = Db::fetchAll($sql.$whr.$sql_odr, $placeholders);
		}
		else
		{
			$sql.= $sql_odr;
			$pages = Db::fetchAll($sql);
		}

		return $pages;
	}

	/**
	 * set reason
	 *
	 * @param  Mixed $reason
	 * @return Array
	 */
	private static function setReason($reason)
	{
		$sql_reason = '';
		$placeholders = array();
		if (is_numeric($reason))
		{
			$sql_reason = ' AND `selection_reason` = ? ';
			$placeholders = array($reason);
		}
		return array($sql_reason, $placeholders);
	}

	/**
	 * set type
	 *
	 * @param  Mixed $type
	 * @return Array
	 */
	private static function setType($type)
	{
		$sql_type = '';
		$placeholders = array();
		$types = Values::getTypes();
		if (array_key_exists(strtolower($type), $types))
		{
			$sql_type = ' AND `type` = ? ';
			$placeholders = array_merge($placeholders, array($types[$type]));
		}
		return array($sql_type, $placeholders);
	}

	/**
	 * set Search
	 *
	 * @param  Array $words
	 * @return Array
	 */
	private static function setSearch($words)
	{
		$sql_like = '';
		$placeholders = array();

		if ( ! empty($words))
		{
			$sql_likes = array();
			foreach ($words as $word)
			{
				$word = '%'.$word.'%';
				$sql_likes[] = '`url` LIKE ? OR `title` LIKE ?';
				$placeholders = array_merge($placeholders, array($word, $word));
			}
			$sql_like = ' AND ('.join(' OR ', $sql_likes).') ';
		}

		return array($sql_like, $placeholders);
	}

	/**
	 * set Search
	 *
	 * @param  String $orderby
	 * @return String
	 */
	private static function setOrder($orderby)
	{
		$order = 'DESC';
		$by    = 'created_at';
		$order_whitelist = array(
			'seq_asc',
			'seq_desc',
			'created_at_asc',
			'created_at_desc',
			'date_asc',
			'date_desc',
			'url_asc',
			'url_desc',
			'page_asc',
			'page_desc'
		);

		if (in_array($orderby, $order_whitelist))
		{
			$order = strtoupper(substr($orderby, strrpos($orderby, '_') + 1));
			$by    = strtolower(substr($orderby, 0, strrpos($orderby, '_')));
		}
		$sql_odr = ' order by `'.$by.'` '.$order.';';

		return $sql_odr;
	}

	/**
	 * fetch page from db
	 *
	 * @param  String $url
	 * @param Bool $force
	 * @return Bool|Array
	 */
	public static function fetchPage($url, $force = false)
	{
		if (isset(static::$loaded_pages[$url]) && ! $force) return static::$loaded_pages[$url];
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Db::versionSql().';';
		static::$loaded_pages[$url] = Db::fetch($sql, array($url));
		if ( ! static::$loaded_pages[$url]) return false;

		// titleless
		if (empty(static::$loaded_pages[$url]['title']))
		{
			$title = Html::fetchPageTitle($url);
			static::$loaded_pages[$url]['title'] = $title;
			self::updateField($url, 'title', $title);
		}
		return static::$loaded_pages[$url];
	}

	/**
	 * add each page
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function addPage($url)
	{
		$url = Util::urldec($url);

		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Db::currentVersionSql().';';

		if (Db::fetch($sql, array($url)))
		{
			Session::add(
				'messages',
				'errors',
				A11YC_LANG_PAGES_ALREADY_EXISTS.': '. Util::s($url));
			return false;
		}

		$title = '';
		if (Guzzle::envCheck())
		{
			$title = Html::fetchPageTitle($url);
		}

		if ( ! $title)
		{
			Session::add(
				'messages',
				'errors',
				A11YC_LANG_ERROR_COULD_NOT_GET_HTML.': '. Util::s($url));
		}

		$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
		$sql.= ' (`url`, `created_at`, `title`) VALUES (?, ?, ?);';
		return Db::execute($sql, array($url, date('Y-m-d H:i:s'), $title));
	}

	/**
	 * update page field
	 *
	 * @param  String $url
	 * @param  String $field
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($url, $field, $value)
	{
		if( ! self::fetchPage($url))
		{
			self::addPage($url);
		}

		$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `'.$field.'` = ?';
		$sql.= ' WHERE `url` = ?'.Db::currentVersionSql();
		return Db::execute($sql, array($value, $url));
	}

	/**
	 * insert page
	 *
	 * @param  Array $vals
	 * @return Bool
	 */
	public static function insert($vals)
	{
		$url = Arr::get($vals, 'url', '');
		if (empty($url)) return false;

		$sql = 'INSERT INTO '.A11YC_TABLE_PAGES.' (';
		$sql.= '`url`,';
		$sql.= '`alt_url`,';
		$sql.= '`type`,';
		$sql.= '`title`,';
		$sql.= '`level`,';
		$sql.= '`standard`,';
		$sql.= '`selection_reason`,';
		$sql.= '`done`,';
		$sql.= '`trash`,';
		$sql.= '`date`,';
		$sql.= '`created_at`,';
		$sql.= '`updated_at`,';
		$sql.= '`version`,';
		$sql.= '`seq`';
		$sql.= ')';
		$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
		return Db::execute(
			$sql,
			array(
				$url,
				Arr::get($vals, 'alt_url', ''),
				intval(Arr::get($vals, 'type', 0)),
				Arr::get($vals, 'title', ''),
				intval(Arr::get($vals, 'level', 0)),
				intval(Arr::get($vals, 'standard', 0)),
				intval(Arr::get($vals, 'selection_reason', 0)),
				intval(Arr::get($vals, 'done', 0)),
				intval(Arr::get($vals, 'trash', 0)),
				Arr::get($vals, 'date', ''),
				Arr::get($vals, 'created_at', ''),
				Arr::get($vals, 'updated_at', ''),
				Arr::get($vals, 'version', 0), // do not intval()
				intval(Arr::get($vals, 'seq', 0))
			)
		);
	}

	/**
	 * delete
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function delete($url)
	{
		$url = Util::urldec($url);
		return self::updateField($url, 'trash', 1);
	}

	/**
	 * undelete
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function undelete($url)
	{
		$url = Util::urldec($url);
		return self::updateField($url, 'trash', 0);
	}

	/**
	 * purge
	 *
	 * @param  String $url
	 * @return Bool
	 */
	public static function purge($url)
	{
		$url = Util::urldec($url);

		$sql = 'DELETE FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Db::currentVersionSql();
		return Db::execute($sql, array($url));
	}

	/**
	 * count pages
	 *
	 * @return String $type
	 * @return Integer|Bool
	 */
	public static function count($type = 'all')
	{
		// count all
		$cntsql = 'SELECT count(url) AS num FROM '.A11YC_TABLE_PAGES.' WHERE `url` <> "" AND ';

		switch ($type)
		{
			case 'yet':
				$ret = Db::fetch($cntsql.'`done` = 0 AND `trash` = 0 '.Db::currentVersionSql());
				break;
			case 'done':
				$ret = Db::fetch($cntsql.'`done` = 1 and `trash` = 0 '.Db::currentVersionSql());
				break;
			case 'trash':
				$ret = Db::fetch($cntsql.'`trash` = 1 '.Db::currentVersionSql());
				break;
			default:
				$ret = Db::fetch($cntsql.'`trash` = 0 '.Db::currentVersionSql());
				break;
		}

		return isset($ret['num']) ? intval($ret['num']) : false;
	}
}
