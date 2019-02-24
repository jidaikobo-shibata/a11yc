<?php
/**
 * A11yc\Sitecheck\ContainNotHeadings
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Sitecheck;

use A11yc\Model;
use A11yc\Element;

class ContainNotHeadings
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
			$str = Element\Get::ignoredHtml($page['url']);
			preg_match_all('/\<h[1-6][^\>]*?\>.+?\<\/h[1-6]\>/ims', $str, $ms);
			if (empty($ms[0][0]))
			{
				$pages[] = $page;
			}
		}
		return $pages;
	}
}
