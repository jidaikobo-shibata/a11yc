<?php
/**
 * A11yc\Controller\Bulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Bulk extends Checklist
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::check('bulk');
	}

	/**
	 * dbio
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function dbio($url)
	{
		if ($url != 'bulk') Util::error();

		if (Input::isPostExists())
		{
			// update default only
			Model\Bulk::setDefault();

			// update all
			if (Input::post('update_all') == 1) return;

			// update all
			Model\Bulk::all();
		}
	}
}
