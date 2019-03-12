<?php
/**
 * A11yc\Model\Page
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Page
{
	protected static $loaded_pages = array();
	public static $fields = array(
		'alt_url'          => '',
		'type'             => 0,
		'title'            => '',
		'real_title'       => '',
		'level'            => 0,
		'standard'         => 0,
		'selection_reason' => 0,
		'done'             => 0,
		'trash'            => 0,
		'date'             => '',
		'created_at'       => '', // date('Y-m-d H:i:s')
		'updated_at'       => '',
		'seq'              => 0,
		'serial_num'       => '' // must be string
	);

	/**
	 * fetch
	 *
	 * @param Bool $args
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($args = array(), $force = false)
	{
//		$is_current   = Arr::get($args, 'is_current', true);

		$pages = Data::fetch('page', '*', array(), $force);
		$pages = is_array($pages) ? $pages : array();

		foreach (array_keys($pages) as $url)
		{
			$pages[$url]['url'] = $url;
		}

		// list - yet, done, trash, all
		$pages = self::filterList($pages, Arr::get($args, 'list', 'all'));

		// reason and type
		$pages = self::filterPage($pages, 'reason', Arr::get($args, 'reason', null));
		$pages = self::filterPage($pages, 'type', Arr::get($args, 'type', null));

		// order
		$pages = self::setOrder($pages, Arr::get($args, 'order', 'seq_asc'));

		// search
		$words = Arr::get($args, 'words', array());
		$words = is_array($words) ? $words : array();
		$pages = self::filterSearch($pages, $words);

		return $pages;
	}

	/**
	 * list
	 *
	 * @param Array $pages
	 * @param String $list
	 * @return Array
	 */
	private static function filterList($pages, $list = 'all')
	{
		$vals = array();
		foreach ($pages as $page)
		{
			if (
				($list == 'all'   && $page['trash'] == 0) ||
				($list == 'yet'   && $page['trash'] == 0 && $page['done'] == 0) ||
				($list == 'done'  && $page['trash'] == 0 && $page['done'] == 1) ||
				($list == 'trash' && $page['trash'] == 1)
			)
			{
				$vals[] = $page;
			}
		}

		return $vals;
	}

	/**
	 * reason and type
	 * reason: 1 typical, 2 random, 3 because of All, 4 popular, 5 new, etc
	 * type: html, pdf
	 *
	 * @param Array $pages
	 * @param String $type reason, type
	 * @param String|Null $cond
	 * @return Array
	 */
	private static function filterPage($pages, $type, $cond = null)
	{
		if (is_null($cond)) return $pages;
		$field = $type == 'reason' ? 'selection_reason' : 'type';

		$vals = array();
		foreach ($pages as $page)
		{
			if ($page[$field] == $cond)
			{
				$vals[] = $page;
			}
		}

		return $vals;
	}

	/**
	 * set Order
	 *
	 * @param Array $pages
	 * @param String $orderby
	 * @return Array
	 */
	private static function setOrder($pages, $orderby)
	{
		$order = strtoupper(substr($orderby, strrpos($orderby, '_') + 1));
		$by = strtolower(substr($orderby, 0, strrpos($orderby, '_')));
		if ( ! in_array($order, array('ASC', 'DESC'))) return $pages;
		return Util::multisort($pages, $by, $order);
	}

	/**
	 * search
	 *
	 * @param Array $pages
	 * @param Array $words
	 * @return Array
	 */
	private static function filterSearch($pages, $words)
	{
		if (empty($words)) return $pages;
		$vals = array();
		foreach ($pages as $page)
		{
			foreach ($words as $word)
			{
				if (
					mb_strpos($page['title'], $word) !== false ||
					mb_strpos($page['url'], $word) !== false
				)
				{
					$vals[] = $page;
				}
			}
		}
		return $vals;
	}

	/**
	 * fetch
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return Bool|Array
	 */
	public static function fetch($url, $force = false)
	{
		return Data::fetch('page', Util::urldec($url), array(), $force);
	}

	/**
	 * fetch by dbid
	 *
	 * @param Integer $dbid
	 * @return Bool|Array
	 */
	public static function fetchByDbid($dbid)
	{
		foreach (static::fetchAll() as $v)
		{
			if ($v['dbid'] != $dbid) continue;
			return $v;
		}
		return false;
	}

	/**
	 * add each page
	 *
	 * @param String $url
	 * @return Bool
	 */
	public static function addPage($url)
	{
		$url = Util::urldec($url);

		if (array_key_exists($url, static::fetchAll()))
		{
			Session::add(
				'messages',
				'errors',
				A11YC_LANG_CTRL_ALREADY_EXISTS.': '. Util::s($url));
			return false;
		}

		$html = Html::fetchHtmlFromInternet($url);
		$title = Html::pageTitleFromHtml($html);

		if (empty($title))
		{
			Session::add('messages', 'errors', A11YC_LANG_ERROR_COULD_NOT_GET_HTML.Util::s($url));
		}

		$vals = array();
		$vals['title'] = $title;

		return static::insert($url, $vals);
	}

	/**
	 * insert page
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Bool
	 */
	public static function insert($url, $vals)
	{
		foreach (static::$fields as $key => $default)
		{
			$vals[$key] = Arr::get($vals, $key, $default);
		}
		$vals['updated_at'] = empty($vals['updated_at']) ?
												date('Y-m-d H:i:s') :
												date('Y-m-d H:i:s', strtotime($vals['updated_at']));

		return Data::insert('page', $url, $vals);
	}

	/**
	 * updatePartial page field
	 *
	 * @param String $url
	 * @param String $field
	 * @param Mixed  $value
	 * @return Bool
	 */
	public static function updatePartial($url, $field, $value)
	{
		$page = static::fetch($url, true);
		if( ! $page)
		{
			self::addPage($url);
		}
		$page[$field] = $value;
		return Data::update('page', $url, $page);
	}

	/**
	 * delete
	 *
	 * @param String $url
	 * @return Bool
	 */
	public static function delete($url)
	{
		$url = Util::urldec($url);
		return self::updatePartial($url, 'trash', 1);
	}

	/**
	 * undelete
	 *
	 * @param String $url
	 * @return Bool
	 */
	public static function undelete($url)
	{
		$url = Util::urldec($url);
		return self::updatePartial($url, 'trash', 0);
	}

	/**
	 * purge
	 *
	 * @param String $url
	 * @return Bool
	 */
	public static function purge($url)
	{
		return Data::delete('page', $url);
	}

	/**
	 * count pages
	 *
	 * @return String $type
	 * @return Integer|Bool
	 */
	public static function count($type = 'all')
	{
		return count(static::fetchAll(array('list' => $type)));
	}
}
