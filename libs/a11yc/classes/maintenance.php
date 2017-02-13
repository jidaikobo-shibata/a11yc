<?php
/**
 * A11yc\Arr
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
	private static $github_api = 'https://api.github.com/repos/jidaikobo-shibata/a11yc';

	/**
	 * leave at least a day
	 *
	 * @return  void
	 */
	public static function leave_at_least_a_day ()
	{
		// run once in a day
		$checked_file = A11YC_CACHE_PATH.'/checked';
		$cachetime = file_exists($checked_file) ? filemtime($checked_file) : 0;
		if ($cachetime >= time() - 86400)
		{
			return false;
		}

		// update check date flag
		touch($checked_file);
		return true;
	}

	/**
	 * version check
	 *
	 * @return  void
	 */
	public static function version_check ()
	{
		// version notice file
		$version_notice_file = A11YC_CACHE_PATH.'/version_notice';

		// ask Github API and update stored version
		ini_set('user_agent', 'file_get_contents');
		$tags = json_decode(
			file_get_contents(static::$github_api.'/tags'),
			true);
		$max = $tags[max(array_keys($tags))];

		// is lower?
		if (version_compare(A11YC_VERSION, $max['name']) == -1)
		{
			if ( ! preg_match('/[\n\.]+/', $max['name']))
			{
				Util::error('Not Found.');
			}
			file_put_contents($version_notice_file, $max['name']);
			return;
		}

		// using up to date version
		file_exists($version_notice_file) and unlink($version_notice_file);
		return;
	}

	/**
	 * compare with stored version
	 *
	 * @return  void
	 */
	public static function is_uging_lower ()
	{
		return static::get_stored_version() ? true : false;
	}

	/**
	 * get stored version
	 *
	 * @return  void
	 */
	public static function get_stored_version ()
	{
		static $stored_version = '';
		if (empty($stored_version) === false) return $stored_version;

		$version_notice_file = A11YC_CACHE_PATH.'/version_notice';
		if (file_exists($version_notice_file))
		{
			$stored_version = file_get_contents($version_notice_file);
			// using lower version
			if (version_compare(A11YC_VERSION, $stored_version) == -1)
			{
				return $stored_version;
			}
		}

		// non existence of file means using newer version
		return false;
	}

	/**
	 * self upgrade
	 *
	 * @return  void
	 */
	public static function self_upgrade ()
	{
	}
}