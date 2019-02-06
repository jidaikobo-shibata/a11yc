<?php
/**
 * A11yc\Controller\Result
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Result
{
	use ResultPage;
	use ResultReport;
	use ResultAll;
	use ResultEach;

	/**
	 * each
	 *
	 * @return Void
	 */
	public static function actionEach()
	{
		$url = Util::enuniqueUri(Input::param('url', '', FILTER_VALIDATE_URL));
		static::each($url);
	}

	/**
	 * page
	 *
	 * @return Void
	 */
	public static function actionPage()
	{
		// use ResultPage
		static::page();
	}

	/**
	 * links
	 *
	 * @param String $base_url
	 * @return Void
	 */
	public static function assignLinks($base_url = '')
	{
		$removes = array('a11yc_policy', 'a11yc_report', 'a11yc_page', 'url');
		if ( ! array_key_exists(Input::get('a11yc_version'), Model\Version::fetchAll()))
		{
			$removes[] = 'a11yc_version';
		}
		$url = empty($base_url) ? Util::uri() : $base_url;
		$url = Util::removeQueryStrings($url, $removes);

		// links
		$results_link = Util::removeQueryStrings($url, array('a11yc_version'));

		$policy_link = $url;

		$report_link = Util::addQueryStrings(
			$url,
			array(
				array('a11yc_report', 1)
			));

		$pages_link = Util::addQueryStrings(
			$url,
			array(
				array('a11yc_page', 1)
			));

		$chk_link = Util::addQueryStrings(
			$url,
			array(
				array('a11yc_each', 1)
			));

		// download
		$download_link = Util::removeQueryStrings(
			Util::uri(),
			array('a11yc_policy', 'a11yc_report', 'a11yc_page', 'url', 'a11yc_version', 'a')
		);

		$download_link = Util::addQueryStrings(
			$download_link,
			array(
				array('a', 'download')
			));

		View::assign('download_link', $download_link);
		View::assign('results_link',  $results_link);
		View::assign('policy_link',   $policy_link);
		View::assign('report_link',   $report_link);
		View::assign('pages_link',    $pages_link);
		View::assign('chk_link',      $chk_link);
	}

	/**
	 * assign levels
	 *
	 * @param String $target_level
	 * @param String $level
	 * @return Void
	 */
	public static function assignLevels($target_level, $level = null)
	{
		// site level
		View::assign(
			'site_level',
			Evaluate::resultStr(Evaluate::checkSiteLevel(), $target_level)
		);

		// Alternative Content Exception
		View::assign('site_alt_exception', Evaluate::checkAltUrlException() ?
			' ('.A11YC_LANG_ALT_URL_EXCEPTION.')' :
			''
		);

		if ( ! is_null($level))
		{
			// Non-Interferences
			View::assign('level', $level == -1 ?
				A11YC_LANG_CHECKLIST_CONFORMANCE_FAILED :
				Evaluate::resultStr($level, $target_level)
			);
		}
	}

	/**
	 * assignResults
	 *
	 * @param Integer $target_level
	 * @param String $url
	 * @return Void
	 */
	public static function assignResults($target_level, $url = '')
	{
		$results = empty($url) ? Evaluate::evaluateTotal() : Evaluate::evaluateUrl($url) ;

		// result - target level
		self::partResult($results, $target_level, $url);
		$result = View::fetch('result');

		// result - additional level
		$additional = '';
		if ($target_level != 3)
		{
			self::partResult($results, $target_level, '', false);
			$additional = View::fetch('result');
		}

		View::assign('result', $result, false);
		View::assign('additional', $additional, false);
	}

	/**
	 * part of result html
	 *
	 * @param Array   $results
	 * @param Integer $target_level
	 * @param String  $url
	 * @param Bool    $include
	 * @return Void
	 */
	private static function partResult($results, $target_level, $url = '', $include = TRUE)
	{
		View::assign(
			'non_exist_and_passed_criterions',
			Model\Setting::fetch('non_exist_and_passed_criterions')
		);

		View::assign('cs',           Model\Checklist::fetch($url));
		View::assign('results',      $results);
		View::assign('target_level', $target_level);
		View::assign('include',      $include);
		View::assign('yml',          Yaml::fetch(), FALSE);
		View::assign('result',       View::fetchTpl('result/inc_each_criterions_checklist.php'), FALSE);
	}
}
