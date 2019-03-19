<?php
/**
 * A11yc\Sitecheck\ContainNotHref
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

class ContainNotHref
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
			preg_match_all('/\<a [^\>]*?\>/ims', $str, $ms);
			if (empty($ms[0])) return;
			foreach ($ms[0] as $v)
			{
				if (strpos(strtolower($v), 'href') !== false) continue;
				$pages[] = $page;
			}
		}
		return $pages;
	}
}
