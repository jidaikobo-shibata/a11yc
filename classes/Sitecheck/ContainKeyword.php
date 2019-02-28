<?php
/**
 * A11yc\Sitecheck\ContainKeyword
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Sitecheck;

use A11yc\Model;

class ContainKeyword
{
	/**
	 * check
	 *
	 * @return Array
	 */
	public static function check()
	{
		$keyword = Input::post('keyword', '');
		$use_re = Input::post('use_re', false);

		if (self::minKeyword($keyword) === false) return array();
		if ($use_re && self::validRe($keyword) === false) return array();

		$pages = array();
		foreach (Model\Page::fetchAll() as $page)
		{
			$html = Model\Html::fetch($page['url']);

			if ($use_re == false && strpos($html, $keyword) === false) continue;
			if ($use_re && preg_match($keyword, $html) === 0) continue;
			$pages[] = $page;
		}
		return $pages;
	}

	/**
	 * minimum
	 *
	 * @param String $keyword
	 * @return Bool
	 */
	private static function minKeyword($keyword)
	{
		if (strlen($keyword) <= 2)
		{
			Session::add('messages', 'errors', A11YC_LANG_SITECHECK_ERR_SHORT_KEYWORD);
			return false;
		}
		return true;
	}

	/**
	 * re
	 *
	 * @param String $keyword
	 * @return Bool
	 */
	private static function validRe($keyword)
	{
		$delimiter = substr($keyword, 0, 1);
		if (strpos($keyword, $delimiter, 1) === false)
		{
			Session::add('messages', 'errors', A11YC_LANG_SITECHECK_ERR_WRONG_DELIMITER);
			return false;
		}

		$test = @preg_match($keyword, 'text');
		if ($test === false)
		{
			Session::add('messages', 'errors', A11YC_LANG_SITECHECK_ERR_WRONG_RE);
			return false;
		}
		return true;
	}
}
