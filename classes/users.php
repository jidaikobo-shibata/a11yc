<?php
/**
 * A11yc\Users
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Users
{
	/**
	 * fetch_users
	 *
	 * @return  array()
	 */
	public static function fetch_users()
	{
		static $users = array();
		if ($users) return $users;
		require_once(A11YC_PATH.'/config/users.php');
		return $users;
	}

	/**
	 * fetch_current_user
	 *
	 * @return  array()
	 */
	public static function fetch_current_user()
	{
		if ( ! isset($_SESSION['uid'])) return array();
		$uid = $_SESSION['uid'];
		$users = static::fetch_users();
		foreach ($users as $k => $v)
		{
			$users[$k]['id'] = $k;
		}
		return isset($users[$uid]) ? $users[$uid] : array();
	}
}
