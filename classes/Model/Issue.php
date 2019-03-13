<?php
/**
 * A11yc\Model\Issue
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

use A11yc\Element;

class Issue
{
	protected static $vals = null;
	public static $fields = array(
		'title'         => '',
		'is_common'     => false,
		'criterion'     => '1-1-1',
		'html'          => '',
		'n_or_e'        => 0,
		'status'        => 0,
		'techs'         => array(),
		'other_urls'    => '',
		'page_ids'      => array(),
		'criterions'    => array(),
		'memo'          => '',
		'error_message' => '',
		'uid'           => 1,
		'seq'           => 0,
		'trash'         => 0,
		'output'        => true,
		'bbs'           => array(),
	);

	/**
	 * fetch all
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$vals) && ! $force) return static::$vals;
		$vals = array();
		$commons = array();
		foreach (Data::fetchArr('issue', '*', array(), $force) as $url => $val)
		{
			foreach ($val as $v)
			{
				$v['url'] = $url;
				$v = Data::filter($v, static::$fields);

				$criterion = empty($v['criterion']) ? '1-1-1' : $v['criterion'];
				if ($v['is_common'] == 1 || empty($url))
				{
					$commons[$criterion][] = $v;
					continue;
				}
				$vals[$url][$criterion][] = $v;
			}
		}

		$vals = self::setCommonIssue($vals, $commons);

		$commons = self::issueOrder($commons);
		static::$vals = array();
		if ( ! empty($commons))
		{
			static::$vals['commons'] = self::issueOrder($commons);
		}
		static::$vals = array_merge(static::$vals, self::setIssueOrder($vals));

		return static::$vals;
	}

	/**
	 * set common issue
	 *
	 * @param Array $issues
	 * @param Array $commons
	 * @return Array
	 */
	private static function setCommonIssue($issues, $commons)
	{
		foreach ($commons as $v)
		{
			foreach ($v as $vv)
			{
				foreach ($vv['page_ids'] as $page_id)
				{
					if ($page = Page::fetchByDbid($page_id))
					{
//						if ( ! isset($issues[$page['url']])) continue;
						$issues[$page['url']]['common'][0]['seq'] = -10000;
						$issues[$page['url']]['common'][0]['output'] = true;
						$issues[$page['url']]['common'][0]['trash'] = 0;
						$issues[$page['url']]['common'][0]['status'] = 0;
						$issues[$page['url']]['common'][0]['id'] = 0;
						$issues[$page['url']]['common'][0]['issue_ids'][] = $vv['id'];
					}
				}
			}
		}

		return $issues;
	}

	/**
	 * set order
	 *
	 * @param Array $issues
	 * @return Array
	 */
	private static function issueOrder($issues)
	{
		foreach ($issues as $criterion => $v)
		{
			$v = Util::multisort($v, 'seq');
			$vals = array();
			foreach ($v as $vv)
			{
				$vals[$vv['id']] = $vv;
			}
			$issues[$criterion] = $vals;
		}
		return $issues;
	}

	/**
	 * set issue order
	 *
	 * @param Array $vals
	 * @return Array
	 */
	private static function setIssueOrder($vals)
	{
		foreach ($vals as $url => $val)
		{
			$vals[$url] = self::issueOrder($val);
		}

		// Page::fetchAll() returns sqe ordered array
		$retvals = array();
		foreach (Page::fetchAll() as $v)
		{
			$url = $v['url'];
			if ( ! isset($vals[$url])) continue;
			$retvals[$url] = $vals[$url];
		}
		return $retvals;
	}

	/**
	 * fetch
	 *
	 * @param Integer $id
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($id, $force = false)
	{
		$vals = static::fetchAll($force);
		foreach ($vals as $val)
		{
			foreach ($val as $v)
			{
				if (array_key_exists($id, $v))
				{
					return Arr::get($v, $id);
				}
			}
		}
		return array();
	}

	/**
	 * fetch by url
	 *
	 * @param String $url
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchByUrl($url, $force = false)
	{
		$vals = static::fetchAll($force);
		return Arr::get($vals, $url, array());
	}

	/**
	 * fetch by status
	 *
	 * @param String $status [0 not yet, 1 in progress, 2 finish, 3 trashed]
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchByStatus($status, $force = false)
	{
		$vals = static::fetchAll($force);

		$retvals = array();
		foreach ($vals as $url => $val)
		{
			foreach ($val as $criterion => $each)
			{
				foreach ($each as $k => $v)
				{
					if (
						$status < 3 && $v['status'] == $status && $v['trash'] == 0 ||
						$status == 3 && $v['trash'] == 1
					)
					{
						$retvals[$url][$criterion][$k] = $v;
					}
				}
			}
		}
		return $retvals;
	}

	/**
	 * fetch for checklist
	 *
	 * @param String $url
	 * @param String $criterion
	 * @return Array
	 */
	public static function fetch4Checklist($url, $criterion)
	{
//		$vals = static::fetchAll();
		$vals = static::fetchByStatus(0);

		return isset($vals[$url][$criterion]) ? $vals[$url][$criterion] : array();
	}

	/**
	 * fetch for Validation
	 *
	 * @param String $url
	 * @param String $html
	 * @return Array
	 */
	public static function fetch4Validation($url, $html)
	{
		$vals = static::fetchAll();
		$html = empty($html) ? Element\Get\Each::firstTag(Element\Get::ignoredHtml($url)) : $html;

		foreach (array($url, 'commons') as $key)
		{
			foreach (Arr::get($vals, $key, array()) as $v)
			{
				foreach ($v as $each)
				{
					if (
						Arr::get($each, 'html') == $html &&
						Arr::get($each, 'trash') == 0 &&
						Arr::get($each, 'status') <= 1
					)
					{
						return $each;
					}
				}
			}
		}
		return array();
	}

	/**
	 * valueFilter
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Bool
	 */
	public static function valueFilter($url, $vals)
	{
		// $vals['html'] = empty($vals['html']) ?
		// 							Element\Get\Each::firstTag(Element\Get::ignoredHtml($url)) :
		// 							$vals['html'];
		$vals['is_common'] = $url == 'common' ? true : $vals['is_common'];
		return $vals;
	}

	/**
	 * insert
	 *
	 * @param String $url
	 * @param Array $vals
	 * @return Integer|Bool
	 */
	public static function insert($url, $vals)
	{
		if (empty($url)) return false;
		foreach (static::$fields as $key => $default)
		{
			$vals[$key] = Arr::get($vals, $key, $default);
		}
		$vals = static::valueFilter($url, $vals);

		return Data::insert('issue', $url, $vals);
	}

	/**
	 * update issue
	 *
	 * @param String $url
	 * @param Integer $id
	 * @param Array $vals
	 * @return Bool
	 */
	public static function update($url, $id, $vals)
	{
		$value = static::fetch($id, true);
		foreach ($vals as $k => $v)
		{
			$value[$k] = $v;
		}
		$value = static::valueFilter($url, $value);
		return Data::updateById($id, $value);
	}

	/**
	 * update issue partial
	 *
	 * @param Integer $id
	 * @param String $key
	 * @param Mixed $value
	 * @return Bool
	 */
	public static function updatePartial($id, $key, $value)
	{
		$vals = static::fetch($id, true);
		$vals[$key] = $value;
		return Data::updateById($id, $vals);
	}

	/**
	 * purge
	 *
	 * @param Integer $id
	 * @return Bool
	 */
	public static function purge($id)
	{
		return Data::deleteById($id);
	}
}
