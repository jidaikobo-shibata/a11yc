<?php
/**
 * Kontiki\Users
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Users
{
	protected static $users = array();

	/**
	 * set users
	 * key must be started with 1 (not 0)
	 *
	 * @param  Array array(1 => array(username, password, display_name, memo))
	 * @return Void
	 */
	public static function forge($users = array())
	{
		static::$users = $users;
	}

	/**
	 * fetch_users
	 *
	 * @return Array
	 */
	public static function fetch_users()
	{
		return static::$users;
	}

	/**
	 * fetch_current_user
	 *
	 * @return Array
	 */
	public static function fetch_current_user()
	{
		if ( ! Session::show('auth', 'uid')[0]) return array();
		$uid = Session::show('auth', 'uid')[0];
		$users = static::fetch_users();
		foreach (array_keys($users) as $k)
		{
			$users[$k]['id'] = $k;
		}
		return isset($users[$uid]) ? $users[$uid] : array();
	}
}
