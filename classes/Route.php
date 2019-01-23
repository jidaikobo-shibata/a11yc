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
	 * @param String $controller
	 * @param String $action
	 * @return Void
	 */
	public static function forge($controller = '\A11yc\Controller\Center', $action = 'actionIndex')
	{
		// vals
		$c = Input::get('c', '');
		$a = Input::get('a', '');
		$is_index = empty($c) && empty($a);

		// auth
		if ($is_index && ! Auth::auth())
		{
			$controller = '\A11yc\Controller\Auth';
			$action = 'actionLogin';
		}

		// safe access?
		if ( ! $is_index && ctype_alnum($c) && ctype_alnum($a))
		{
			$controller = '\\A11yc\\Controller\\'.ucfirst($c);
			$action = 'action'.ucfirst($a);
		}

		// guest users only can access auth
		if ( ! Auth::auth() && substr($action, -5) != 'Login')
		{
			$controller = '';
		}

		// performed IPs
		if (defined('A11YC_APPROVED_IPS'))
		{
			if ( ! in_array(Input::server('REMOTE_ADDR'), unserialize(A11YC_APPROVED_IPS)))
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
	 * get controller
	 *
	 * @return String
	 */
	public static function getController()
	{
    return static::$controller;
	}

	/**
	 * get action
	 *
	 * @return String
	 */
	public static function getAction()
	{
    return static::$action;
	}

	/**
	 * set controller
	 *
	 * @return Void
	 */
	public static function setController($controller)
	{
		static::$controller = $controller;
	}

	/**
	 * set action
	 *
	 * @param String $action
	 * @return Void
	 */
	public static function setAction($action)
	{
		static::$action = $action;
	}
}
