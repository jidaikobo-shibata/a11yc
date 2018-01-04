<?php
/**
 * A11yc\Route
 *
 * @package    part of A11yc
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
	 * @param  String $controller
	 * @param  String $action
	 * @return Void
	 */
	public static function forge($controller = '\A11yc\Controller_Center', $action = 'Action_Index')
	{
		// vals
		$c = Input::get('c', '');
		$a = Input::get('a', '');
		$is_index = empty(join($_GET));

		// auth
		if ($is_index && ! \Kontiki\Auth::auth())
		{
			$controller = '\A11yc\Controller_Auth';
			$action = 'Action_Login';
		}

		// safe access?
		if ( ! $is_index && ctype_alnum($c) && ctype_alnum($a))
		{
			$controller = '\A11yc\Controller_'.ucfirst($c);
			$action = 'Action_'.ucfirst($a);
		}

		// guest users only can access auth
		if ( ! Auth::auth() && substr($action, -5) != 'Login')
		{
			$controller = '';
		}

		// performed IPs
		if (defined('A11YC_APPROVED_IPS'))
		{
			if ( ! in_array(Arr::get($_SERVER, 'REMOTE_ADDR'), unserialize(A11YC_APPROVED_IPS)))
			{
				$controller = '';
			}
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
	 * @return String
	 */
	public static function get_controller()
	{
    return static::$controller;
	}

	/**
	 * get_action
	 *
	 * @return String
	 */
	public static function get_action()
	{
    return static::$action;
	}

	/**
	 * set_controller
	 *
	 * @return Void
	 */
	public static function set_controller($controller)
	{
		static::$controller = $controller;
	}

	/**
	 * set_action
	 *
	 * @param String $action
	 * @return Void
	 */
	public static function set_action($action)
	{
		static::$action = $action;
	}
}
