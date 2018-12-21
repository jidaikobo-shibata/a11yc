<?php
/**
 * A11yc\Model\Issues
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Issues
{
	protected static $issues = array();

	/**
	 * fetch
	 *
	 * @param  Integer $id
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($id, $force = false)
	{
		if (isset(static::$issues[$id]) && ! $force) return static::$issues[$id];
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES.' WHERE `id` = ?';
		$sql.= Db::currentVersionSql();
		static::$issues[$id] = Db::fetch($sql, array($id));
		return static::$issues[$id];
	}

	/**
	 * fetch by url
	 *
	 * @param  String $url
	 * @return Array
	 */
	public static function fetchByUrl($url = 'common')
	{
		if (isset(static::$issues[$url])) return static::$issues[$url];

		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
		$sql.= $url == 'common' ? ' WHERE (`is_common` = 1 OR `url` = "")' : ' WHERE `url` = ?';
		$sql.= Db::currentVersionSql();
		static::$issues[$url] = $url == 'common' ?
													Db::fetchAll($sql) :
													Db::fetchAll($sql, array($url));
		return static::$issues[$url];
	}

	/**
	 * fetch by status
	 *
	 * @param  String $status [0 not yet, 1 in progress, 2 finish, 3 trashed]
	 * @return Array
	 */
	public static function fetchByStatus($status)
	{
		$rets = array('common' => array());
		if ($status < 3)
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
			$sql.= ' WHERE `status` = ? AND `trash` = 0';
			$sql.= Db::currentVersionSql(true);
			$sql.= ' ORDER BY `url` ASC';
			$items = Db::fetchAll($sql, array($status));
		}
		else
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
			$sql.= ' WHERE `trash` = 1';
			$sql.= Db::currentVersionSql(true);
			$sql.= ' ORDER BY `url` ASC';
			$items = Db::fetchAll($sql);
		}

		foreach ($items as $v)
		{
			$key = $v['is_common'] ? 'common' : $v['url'];
			$rets[$key][] = $v;
		}
		if (empty($rets['common'])) unset($rets['common']);
		return $rets;
	}

	/**
	 * fetch for checklist
	 *
	 * @param  String $url
	 * @param  String $criterion
	 * @return Array
	 */
	public static function fetch4Checklist($url, $criterion)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
		$sql.= ' WHERE `criterion` = ? AND';
		$sql.= ' (`url` = ? OR `is_common` = 1);';
		return Db::fetchAll($sql, array($criterion, $url));
	}

	/**
	 * fetch for Validation
	 *
	 * @param  String $url
	 * @param  String $html
	 * @return Array
	 */
	public static function fetch4Validation($url, $html)
	{
		if (is_array($html)) return array();
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES;
		$sql.= ' WHERE `html` = ? AND';
		$sql.= ' (`url` = ? OR `is_common` = 1);';
		return Db::fetch($sql, array($html, $url));
	}

	/**
	 * insert each page
	 *
	 * @param  Array $args
	 * @return Integer|Bool
	 */
	public static function insert($args)
	{
		$url = Arr::get($args, 'url', '');
		$url = Util::urldec($url);

		$sql = 'INSERT INTO '.A11YC_TABLE_ISSUES;
		$sql.= ' (`is_common`,';
		$sql.= '`url`,';
		$sql.= '`criterion`,';
		$sql.= '`html`,';
		$sql.= '`n_or_e`,';
		$sql.= '`status`,';
		$sql.= '`tech_url`,';
		$sql.= '`error_message`,';
		$sql.= '`uid`,';
		$sql.= '`created_at`,';
		$sql.= '`version`) VALUES ';
		$sql.= '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0);';

		$r = Db::execute(
			$sql,
			array(
				intval(Arr::get($args, 'is_common', '')),
				$url,
				Arr::get($args, 'criterion', ''),
				Arr::get($args, 'html', ''),
				intval(Arr::get($args, 'n_or_e', 0)),
				intval(Arr::get($args, 'status', 0)),
				Arr::get($args, 'tech_url', ''),
				Arr::get($args, 'error_message', ''),
				intval(Arr::get($args, 'uid', 1)),
				date('Y-m-d H:i:s')
			)
		);

		if ( ! $r) return false;

		$sql = 'SELECT `id` FROM '.A11YC_TABLE_ISSUES;
		$sql.= ' ORDER BY `created_at` desc LIMIT 1;';
		$issue_id = Db::fetch($sql);

		return isset($issue_id['id']) ? intval($issue_id['id']) : false;
	}

	/**
	 * update issue field
	 *
	 * @param  Integer $id
	 * @param  String $field
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($id, $field, $value)
	{
		$id = intval($id);
		$issue = self::fetch($id);
		if(empty($issue)) return false;

		$sql = 'UPDATE '.A11YC_TABLE_ISSUES.' SET `'.$field.'` = ?';
		$sql.= ' WHERE `id` = ?';
		return Db::execute($sql, array($value, $id));
	}

	/**
	 * purge
	 *
	 * @param  Integer $id
	 * @return Bool
	 */
	public static function purge($id)
	{
		$id = intval($id);
		$issue = self::fetch($id);
		if(empty($issue)) return false;

		$sql = 'DELETE FROM '.A11YC_TABLE_ISSUES.' WHERE `id` = ?';
		return Db::execute($sql, array($id));
	}
}
