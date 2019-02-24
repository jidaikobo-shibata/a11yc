<?php
/**
 * A11yc\Sitecheck\ContainWithoutAlt
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

class ContainWithoutAlt
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
			$ms = Element\Get::elementsByRe($str, 'ignores', 'imgs');
			if ( ! $ms[1]) continue;

			foreach ($ms[0] as $m)
			{
				$attrs = Element\Get::attributes($m);
				if ( ! array_key_exists('alt', $attrs))
				{
					$pages[] = $page;
					continue 2;
				}
			}
		}
		return $pages;
	}
}
