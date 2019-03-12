<?php
/**
 * A11yc\Model\Ua
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Ua
{
	protected static $uas = null;
	public static $fields = array(
		'id'   => 1,
		'name' => '',
		'str'  => '',
	);

	/**
	 * fetch uas
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($force = false)
	{
		if ( ! is_null(static::$uas) && ! $force) return static::$uas;
		$vals = Setting::fetchArr('user_agents', array(), $force);
		static::$uas = Data::deepFilter($vals, static::$fields);
		return static::$uas;
	}

	/**
	 * dbio
	 *
	 * @return Void
	 */
	public static function dbio()
	{
		if (Input::isPostExists())
		{
		}
	}
}
