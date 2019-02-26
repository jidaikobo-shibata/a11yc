<?php
/**
 * A11yc\Sitecheck\ContainPositiveTabindex
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

class ContainPositiveTabindex
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
			$ms = Element\Get::elementsByRe($str, 'ignores', 'tags', true);
			if ( ! $ms[1]) continue;
			foreach ($ms[0] as $m)
			{
				$attrs = Element\Get::attributes($m);
				if (isset($attrs['tabindex']) && intval($attrs['tabindex']) > 0)
				{
					$pages[] = $page;
					continue 2;
				}
			}
		}
		return $pages;
	}
}
