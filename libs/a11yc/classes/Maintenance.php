<?php
/**
 * A11yc\Maintenance
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Maintenance extends \Kontiki\Maintenance
{
	public static $is_first_of_day = null;
	public static $is_using_lower  = null;
	private static $github_api     = 'https://api.github.com/repos/jidaikobo-shibata/a11yc';

	/**
	 * leave at least a day
	 *
	 * @return Bool
	 */
	public static function isFisrtOfToday ()
	{
		if ( ! is_null(static::$is_first_of_day)) return static::$is_first_of_day;

		// check
		$sql = 'SELECT `last_checked` FROM '.A11YC_TABLE_MAINTENANCE.';';
		$ret = Db::fetch($sql);

		static::$is_first_of_day = true;
		if ( ! $ret)
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_MAINTENANCE.' (`last_checked`) VALUES (?);';
			Db::execute($sql, array(date('Y-m-d')));
		}
		elseif (isset($ret['last_checked']) && strtotime($ret['last_checked']) >= time() - 86400)
		{
			static::$is_first_of_day = false;
		}
		$sql = 'UPDATE '.A11YC_TABLE_MAINTENANCE.' set `last_checked` = ?;';
		Db::execute($sql, array(date('Y-m-d')));
		return static::$is_first_of_day;
	}

	/**
	 * compare with stored version
	 *
	 * @return Bool
	 */
	public static function isUgingLower ()
	{
		if ( ! self::isFisrtOfToday()) return false;
		if ( ! is_null(static::$is_using_lower)) return static::$is_using_lower;

		// ask Github API and update stored version
		ini_set('user_agent', 'file_get_contents');
		$strs = @file_get_contents(static::$github_api.'/tags');

		if ($strs)
		{
			$tags = json_decode($strs, true);
			$max = $tags[min(array_keys($tags))];
			// lower: return true
			static::$is_using_lower = version_compare(A11YC_VERSION, $max['name']) == -1;
			return static::$is_using_lower;
		}

		// couldn't check version
		error_log('Notice: \A11yc\Maintenance::isUgingLower could not get version.');
		return true;
	}
}