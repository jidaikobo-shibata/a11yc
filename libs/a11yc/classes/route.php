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
		// vals
		$controller = '';
		$action = '';
		$c = Input::get('c');
		$a = Input::get('a');

		// default controller
		if ( ! isset($_GET['c']) &&  ! isset($_GET['a']))
		{
			$controller = '\A11yc\Controller_Center';
			$action = 'Action_Index';
		}
		// safe access?
		elseif (ctype_alnum($c) && ctype_alnum($a))
		{
			$controller = '\A11yc\Controller_'.ucfirst($c);
			$action = 'Action_'.ucfirst($a);
		}

		// class and methods exists
		if (
			class_exists($controller) &&
			method_exists($controller, $action) &&
			is_callable($controller.'::'.$action)
		)
		{
			static::$controller = $controller;
			static::$action = $action;
			return;
		}

		// error
		Util::error('service not available.');
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
