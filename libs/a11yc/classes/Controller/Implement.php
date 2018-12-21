<?php
/**
 * A11yc\Controller\Implement
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Implement
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * Show Techs Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('title', A11YC_LANG_IMPLEMENT_TITLE);
		View::assign('body', View::fetchTpl('implement/index.php'), FALSE);
	}
}
