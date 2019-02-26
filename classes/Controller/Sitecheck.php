<?php
/**
 * A11yc\Controller\Sitecheck
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;
use A11yc\Validate;

class Sitecheck
{
	private static $ops = array(
		'contain_tabindex'     => 'ContainTabindex',
		'contain_withoutalt'   => 'ContainWithoutAlt',
		'contain_withoutth'    => 'ContainWithoutTh',
		'contain_not_headings' => 'ContainNotHeadings',
	);

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
	 * index
	 *
	 * @return Void
	 */
	public static function index()
	{
		$pages = array();
		if (Input::isPostExists())
		{
			$op = Input::post('op');
			if (array_key_exists($op, self::$ops))
			{
				$class = '\\A11yc\\Sitecheck\\'.self::$ops[$op];
				$pages = $class::check();
			}
		}

		View::assign('total', count(Model\Page::fetchAll()));
		View::assign('pages', $pages);
		View::assign('title', A11YC_LANG_SITECHECK_TITLE);
		View::assign('body',  View::fetchTpl('sitecheck/index.php'), FALSE);
	}
}
