<?php
/**
 * A11yc\Controller\Center
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Center
{
	use CenterIndex;

	/**
	 * index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		// use CenterIndex
		static::index();
	}
}
