<?php
/**
 * * A11yc\Validate\Check\SamePageTitleInSameSite
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Validate\Check;

use A11yc\Element;
use A11yc\Validate;
use A11yc\Model;

class SamePageTitleInSameSite extends Validate
{
	/**
	 * same page title in same site
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function check($url)
	{
		Validate\Set::log($url, 'same_page_title_in_same_site', self::$unspec, 5);
		if (Validate::$is_partial === true) return;
		Validate\Set::log($url, 'same_page_title_in_same_site', self::$unspec, 1);

		$str = Element\Get::ignoredHtml($url);
		$title = Model\Html::pageTitleFromHtml($str);
		$pages = Model\Page::fetchAll();

		$titles = array();
		$exists = false;
		foreach ($pages as $page)
		{
			if (in_array($title, $titles)) $exists = true;
			$titles[] = Arr::get($page, 'real_title');
		}

		Validate\Set::errorAndLog(
			$exists,
			$url,
			'same_page_title_in_same_site',
			0,
			$title,
			$title
		);

		static::addErrorToHtml($url, 'same_page_title_in_same_site', static::$error_ids[$url]);
	}
}
