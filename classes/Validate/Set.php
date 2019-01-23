<?php
/**
 * A11yc\Validate\Set
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate;

use A11yc\Model;

class Set extends Validate
{
	/**
	 * set Error
	 *
	 * @param String $url
	 * @param String|Array $error_name
	 * @param Integer $count
	 * @param String $id
	 * @param String $str
	 * @return Void
	 */
	public static function error($url, $error_name, $count, $id, $str)
	{
		$log_id = empty($id) ? self::$unspec : $id;
		static::$logs[$url][$error_name][$log_id] = -1;
		static::$error_ids[$url][$error_name][$count]['id'] = $id;
		static::$error_ids[$url][$error_name][$count]['str'] = Util::s($str);
	}

	/**
	 * set Error and Log
	 *
	 * @param Bool|Integer $exp
	 * @param String $url
	 * @param String|Array $error_name
	 * @param Integer $count
	 * @param String $id
	 * @param String $str
	 * @return Void
	 */
	public static function errorAndLog($exp, $url, $error_name, $count, $id, $str)
	{
		$exp = (boolean) $exp;
		if ($exp)
		{
			self::error($url, $error_name, $count, $id, $str);
			return;
		}
		self::log($url, $error_name, $id, 2);
	}

	/**
	 * set Log
	 *
	 * @param String $url
	 * @param String|Array $error_name
	 * @param String $target_str
	 * @param Integer $status
	 * status:
	 * -1 failed
	 *  0 ignore
	 *  1 done
	 *  2 pased
	 *  3 exist
	 *  4 nonexist
	 *  5 skiped
	 * @return Void
	 */
	public static function log($url, $error_name, $target_str, $status)
	{
		if (is_array($error_name))
		{
			foreach ($error_name as $each_error_name)
			{
				static::$logs[$url][$each_error_name][$target_str] = $status;
			}
			return;
		}
		static::$logs[$url][$error_name][$target_str] = $status;
	}
}
