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
	 * @return Bool
	 */
	public static function leave_at_least_a_day ()
	{
		// old version
		if (file_exists(A11YC_CACHE_PATH))
		{
			unlink(A11YC_CACHE_PATH.'/checked');
			unlink(A11YC_CACHE_PATH.'/.htaccess');
			rmdir(A11YC_CACHE_PATH);
		}

		// check
		$sql = 'SELECT `last_checked` FROM '.A11YC_TABLE_MAINTENANCE.';';
		$ret = Db::fetch($sql);
		if ( ! $ret)
		{
			$sql = 'UPDATE '.A11YC_TABLE_MAINTENANCE.' set `last_checked` = '.date('Y-m-d').';';
			Db::execute($sql);
			return true;
		}
		elseif (isset($ret['last_checked']) && strtotime($ret['last_checked']) >= time() - 86400)
		{
			return false;
		}
		return true;
	}

	/**
	 * compare with stored version
	 *
	 * @return Bool
	 */
	public static function is_uging_lower ()
	{
		$sql = 'SELECT `version` FROM '.A11YC_TABLE_MAINTENANCE.';';
		$ret = Db::fetch($sql);
		if ( ! $ret)
		{
			$sql = 'UPDATE '.A11YC_TABLE_MAINTENANCE.' set `version` = '.A11YC_VERSION.';';
			Db::execute($sql);
			$ret = array();
			$ret['version'] = A11YC_VERSION;
		}

		// ask Github API and update stored version
		ini_set('user_agent', 'file_get_contents');
		$tags = json_decode(
			file_get_contents(static::$github_api.'/tags'),
			true
		);
		$max = $tags[max(array_keys($tags))];

		// lower: return true
		return version_compare(A11YC_VERSION, $ret['version']) == -1;
	}

	/**
	 * self upgrade
	 *
	 * @return Void
	 */
	public static function self_upgrade ()
	{
	}
}