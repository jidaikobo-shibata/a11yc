<?php
/**
 * A11yc\Sitecheck\ContainTabindex
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Sitecheck;

use A11yc\Model;

class ContainTabindex
{
	/**
	 * check
	 *
	 * @return Array
	 */
	public static function check()
	{
		$pages = array();
		foreach (Model\Page::fetchAll() as $page)
		{
			$html = Model\Html::fetch($page['url']);
			if (strpos($html, ' tabindex') === false) continue;
			$pages[] = $page;
		}
		return $pages;
	}
}
