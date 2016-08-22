<?php
/**
 * Kontiki\Users
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
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
		require_once(dirname(KONTIKI_CONFIG_PATH).'/users.php');
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
