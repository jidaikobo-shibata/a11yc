<?php
/**
 * A11yc\Controller\Page
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Page
{
	use PageAdd;
	use PageBulk;
	use PageIndex;
	use PageUpdate;

	/**
	 * Show Pages Index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * add Pages
	 *
	 * @return Void
	 */
	public static function actionAdd()
	{
		static::targetPages();
	}

	/**
	 * edit Page
	 *
	 * @return Void
	 */
	public static function actionEdit()
	{
		static::edit();
	}

	/**
	 * count pages
	 *
	 * @return Void
	 */
	public static function count()
	{
		$count = array(
			'all'   => Model\Page::count('all'),
			'yet'   => Model\Page::count('yet'),
			'done'  => Model\Page::count('done'),
			'trash' => Model\Page::count('trash'),
		);

		View::assign('count', $count);
		View::assign('submenu', View::fetchTpl('page/inc_submenu.php'), FALSE);
	}
}
