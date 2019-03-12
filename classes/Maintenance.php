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

use A11yc\Model;

class Maintenance extends \Kontiki\Maintenance
{
	public static $is_first_of_day = null;
	public static $is_using_lower  = null;
	private static $latest_version = null;
	private static $github_api     = 'https://api.github.com/repos/jidaikobo-shibata/a11yc';

	/**
	 * leave at least a day
	 *
	 * @return Bool
	 */
	public static function isFisrtOfToday ()
	{
		if ( ! is_null(static::$is_first_of_day)) return static::$is_first_of_day;

		$today = date('Y-m-d');

		// check
		$last_checked = Model\Setting::fetch('last_checked');

		// check limit for github
		$count_arr = array('date' => $today, 'count' => 0);
		$checktimes = Model\Setting::fetch('checktimes', $count_arr);
		if ($checktimes['date'] == $today && $checktimes['count'] > 5) return false;
		if ($checktimes['date'] == $today)
		{
			$checktimes['count']++;
			Model\Setting::update('checktimes', $checktimes);
			return false;
		}

		Model\Setting::update('checktimes', $count_arr);
		static::$is_first_of_day = true;
		if (empty($last_checked))
		{
			Model\Setting::insert(array(
					'last_checked' => $today
				));
		}
		elseif ( ! empty($last_checked) && strtotime($last_checked) >= time() - 86400)
		{
			static::$is_first_of_day = false;
		}

		if ($last_checked != $today)
		{
			Model\Setting::update('last_checked', $today);
		}
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

		// lower: return true
		$latest = static::getLatestVersion();
		if ($latest !== false)
		{
			static::$is_using_lower = version_compare(A11YC_VERSION, $latest) == -1;
			return static::$is_using_lower;
		}

		// couldn't check version
		error_log('Notice: \A11yc\Maintenance::isUgingLower could not get version.');
		return true;
	}

	/**
	 * get latest version
	 *
	 * @return Bool|String
	 */
	public static function getLatestVersion ()
	{
		if ( ! self::isFisrtOfToday()) return false;
		if ( ! is_null(self::$latest_version)) return self::$latest_version;

		// ask Github API and update stored version
		ini_set('user_agent', 'file_get_contents');
		$strs = @file_get_contents(self::$github_api.'/tags');

		if ($strs)
		{
			$tags = json_decode($strs, true);
			$max = $tags[min(array_keys($tags))];
			self::$latest_version = $max['name'];
			return self::$latest_version;
		}

		self::$latest_version = false;
		return self::$latest_version;
	}

}
