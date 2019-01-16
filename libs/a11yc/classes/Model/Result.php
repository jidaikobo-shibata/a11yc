<?php
/**
 * A11yc\Model\Result
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Result
{
	protected static $results = null;
	public static $fields = array(
		'result' => 0,
		'method' => 0,
		'memo' => '',
		'uid' => 0,
	);

	/**
	 * fetch results from db
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($url, $force = false)
	{
		if (isset(static::$results[$url]) && ! $force) return static::$results[$url];
		static::$results[$url] = Data::fetch('result', $url, array(), $force);
		return static::$results[$url];
	}

	/**
	 * fetch passed results from db
	 *
	 * @param String $url
	 * @return Array
	 */
	public static function fetchPassed($url)
	{
		$results = self::fetch($url, true);
		if ( ! is_array($results)) return array();
		$retval = array();

		foreach ($results as $criterion => $result)
		{
			if (intval($result['result']) === 0) continue;
			if (intval($result['result']) === -1) continue;
			$retval[] = $criterion;
		}
		return $retval;
	}

	/**
	 * pages of passed
	 *
	 * @param Integer|String $target_level
	 * @return Array
	 */
	public static function passedPages($target_level)
	{
		$target_level = intval($target_level);
		$pages = array();
		foreach (Page::fetchAll(array('list' => 'done')) as $page)
		{
			$level = intval(Arr::get($page, 'level', 0));
			if ($level >= $target_level)
			{
				$pages[] = $page;
			}
		}
		return $pages;
	}

	/**
	 * pages of unpassed
	 *
	 * @param Integer|String $target_level
	 * @return Array
	 */
	public static function unpassedPages($target_level)
	{
		$target_level = intval($target_level);
		$pages = array();
		foreach (Page::fetchAll(array('list' => 'done')) as $page)
		{
			$level = intval(Arr::get($page, 'level', 0));
			if ($level > $target_level) continue;
			$pages[] = $page;
		}
		return $pages;
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
		foreach ($vals as $criterion => $val)
		{
			foreach (static::$fields as $key => $default)
			{
				$vals[$criterion][$key] = Arr::get($val, $key, $default);
			}

			// $vals[$criterion] = array(
			// 	'url'       => Arr::get($val, 'url', $url),
			// 	'criterion' => Arr::get($val, 'criterion', ''),
			// 	'memo'      => Arr::get($val, 'memo', ''),
			// 	'uid'       => Arr::get($val, 'uid', 0),
			// 	'result'    => Arr::get($val, 'result', 0),
			// 	'method'    => Arr::get($val, 'method', 0),
			// );
		}

		return Data::insert('result', $url, $vals);
	}

	/**
	 * update results
	 *
	 * @param String $url
	 * @param array  $vals
	 * @return Void
	 */
	public static function update($url, $vals)
	{
		$url = Util::urldec($url);
		Data::delete('result', $url);
		Data::insert('result', $url, $vals);
	}
}
