<?php
/**
 * A11yc\Sitecheck\ContainWithoutTh
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

class ContainWithoutTh
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
			preg_match_all('/\<table[^\>]*?\>.+?\<\/table\>/ims', $str, $ms);
			if ( ! isset($ms[0][0])) continue;
			foreach ($ms[0] as $m)
			{
				preg_match_all('/\<th[^\>]*?\>.+?\<\/th\>/ims', $m, $mms);
				if (empty($mms[0]))
				{
					$pages[] = $page;
					continue 2;
				}
			}
		}
		return $pages;
	}
}
