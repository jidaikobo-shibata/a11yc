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
use A11yc\Model;

class SamePageTitleInSameSite extends Validate
{
	/**
	 * same page title in same site
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		static::setLog($url, 'same_page_title_in_same_site', self::$unspec, 5);
		if (Validate::$is_partial == true) return;
		static::setLog($url, 'same_page_title_in_same_site', self::$unspec, 1);

		$title = Model\Html::fetchPageTitle($url);
		$sql = 'SELECT count(*) as num FROM '.A11YC_TABLE_PAGES.' WHERE `title` = ?';
		$sql.= Db::versionSql().';';
		$results = Db::fetch($sql, array($title));

		if (intval($results['num']) >= 2)
		{
			static::setError($url, 'same_page_title_in_same_site', 0, $title, $title);
		}
		else
		{
			static::setLog($url, 'same_page_title_in_same_site', $title, 2);
		}
		static::addErrorToHtml($url, 'same_page_title_in_same_site', static::$error_ids[$url]);
	}
}
