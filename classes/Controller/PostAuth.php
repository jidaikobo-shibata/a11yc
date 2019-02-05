<?php
/**
 * A11yc\Controller\PostAuth
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

trait PostAuth
{
	/**
	 * auth
	 *
	 * @return Void
	 */
	public static function auth()
	{
		if (A11yc\Auth::auth()) return true;

		// ip check for guest users
		if ( ! self::isInWhiteList())
		{
			// die if at limit
			self::ipCheckForGuestUsers(Input::server('REMOTE_ADDR', ''));
		}
		return true;
	}

	/**
	 * is in white list
	 *
	 * @return Bool
	 */
	public static function isInWhiteList()
	{
		$ip = Input::server('REMOTE_ADDR', '');

		// performed IPs
		$is_in_white_list = false;
		if (defined('A11YC_APPROVED_GUEST_IPS'))
		{
			$is_in_white_list = in_array($ip, unserialize(A11YC_APPROVED_GUEST_IPS));
		}

		return $is_in_white_list;
	}

	/**
	 * count up for guest users
	 *
	 * @return Void
	 */
	public static function countUpForGuestUsers()
	{
		if ( ! A11yc\Auth::auth() && ! self::isInWhiteList())
		{
			// ip
			$sql = 'INSERT INTO ip (ip, datetime) VALUES (?, ?);';
			$ip = Input::server('REMOTE_ADDR', '');
			Db::execute($sql, array($ip, date('Y-m-d H:i:s')), A11YC_POST_DB);

			// cookie (session)
			Session::add('a11yc_post', 'count', time());
		}
	}

	/**
	 * ip check for guest users
	 *
	 * @param String $ip
	 * @return Void
	 */
	private static function ipCheckForGuestUsers($ip)
	{
		// database
		define('A11YC_POST_DB', 'post_log');
		define('A11YC_POST_DATA_FILE', '/'.A11YC_POST_DB.'.sqlite');
		Db::forge(
			A11YC_POST_DB,
			array(
				'dbtype' => 'sqlite',
				'path' => A11YC_DATA_PATH.A11YC_POST_DATA_FILE,
			));
		self::initTable();

		// ip check
		$past_24h = time() - 86400;
		$sql = 'SELECT COUNT(`ip`) as cnt FROM ip WHERE `ip` = ? AND `datetime` > ?;;';
		$ip_count = Db::fetch($sql, array($ip, $past_24h), A11YC_POST_DB);

		// cookie check
		$cookie_count = Session::show('a11yc_post', 'count') ?: array();
		$cookie_count = array_filter($cookie_count, function ($v){return ($v > time() - 600);});

		// ban
		if (
			$ip_count['cnt'] >= A11YC_POST_IP_MAX_A_DAY ||
			count($cookie_count) >= A11YC_POST_COOKIE_A_10MIN
		)
		{
			Util::error('too much accesses.');
		}
	}

	/**
	 * init table
	 *
	 * @return Void
	 */
	private static function initTable()
	{
		if ( ! DB::isTableExist('ip', A11YC_POST_DB))
		{
			$sql = 'CREATE TABLE ip (';
			$sql.= '`ip`        text NOT NULL,';
			$sql.= '`datetime`  datetime';
			$sql.= ');';
			DB::execute($sql, array(), A11YC_POST_DB);
		}
	}
}
