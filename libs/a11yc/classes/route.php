<?php
/**
 * A11yc\Route
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Route extends \Kontiki\Route
{
	protected static $controller;
	protected static $action;

	/**
	 * forge
	 *
	 * @return  void
	 */
	public static function forge()
	{
		$def_controller = '\A11yc\Controller_Center';
		$def_action = 'Action_Index';
		$controller = Input::get('c') ? '\A11yc\Controller_'.ucfirst(Input::get('c')) : $def_controller;
		$action = Input::get('a') ? 'Action_'.Input::get('a') : 'Action_Index';
		if (method_exists($controller, $action) and is_callable($controller.'::'.$action))
		{
			static::$controller = $def_controller;
			static::$action = $def_action;
		}
		static::$controller = $controller;
		static::$action = $action;
	}

	/**
	 * get_controller
	 *
	 * @return  string
	 */
	public static function get_controller()
	{
    return static::$controller;
	}

	/**
	 * get_action
	 *
	 * @return  string
	 */
	public static function get_action()
	{
    return static::$action;
	}
}
