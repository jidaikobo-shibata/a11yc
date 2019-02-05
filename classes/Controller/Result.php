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
	 * @return Void
	 */
	public static function assignLinks()
	{
		$removes = array('a11yc_policy', 'a11yc_report', 'a11yc_pages', 'url');
		if ( ! array_key_exists(Input::get('a11yc_version'), Model\Version::fetchAll()))
		{
			$removes[] = 'a11yc_version';
		}
		$url = Util::removeQueryStrings(Util::uri(), $removes);

		// base page
		$results_link = Util::removeQueryStrings($url, array('a11yc_version'));

		// other pages
		$policy_link = $url;
		$report_link = Util::addQueryStrings(
			$url,
			array(
				array('a11yc_report', 1)
			));
		$pages_link = Util::addQueryStrings(
			$url,
			array(
				array('a11yc_pages', 1)
			));

		// download
		$download_link = Util::removeQueryStrings(
			Util::uri(),
			array('a11yc_policy', 'a11yc_report', 'a11yc_pages', 'url', 'a11yc_version', 'a')
		);

		$download_link = Util::addQueryStrings(
			$download_link,
			array(
				array('a', 'download')
			));

		View::assign('download_link', $download_link);
		View::assign('results_link', $results_link);
		View::assign('policy_link',  $policy_link);
		View::assign('report_link',  $report_link);
		View::assign('pages_link',   $pages_link);
	}

	/**
	 * assign levels
	 *
	 * @param String $target_level
	 * @param String $level
	 * @return Void
	 */
	private static function assignLevels($target_level, $level = null)
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
	 * Show checklist Results
	 *
	 * @param Srting $url
	 * @param Bool $is_assign
	 * @return Bool
	 */
	public static function each($url, $is_assign = false)
	{
		$page = Model\Page::fetch($url);

		if ( ! $page || ! $page['done'] || $page['trash'])
		{
			if ($is_assign) return false;
			header("HTTP/1.0 404 Not Found");
			echo '404 Not Found';
			exit();
		}

		$settings = Model\Setting::fetchAll();
		static::assignLinks();
		self::assignLevels($settings['target_level'], $page['level']);

		// alt checklist link
		if ( ! empty($page['alt_url']))
		{
			$chk = Util::remove_query_strings(Util::uri(), array('url', 'a11yc_pages'));
			$chk = Util::add_query_strings(
				$chk,
				array(array('url', Util::urlenc($page['alt_url'])))
			);
			View::assign(
				'alt_results',
				' ('.sprintf(A11YC_LANG_ALT_URL_LEVEL, $chk).': '.Evaluate::result_str(Evaluate::getLevelByUrl($page['alt_url']), $settings['target_level']).')'
			);
		}
		else
		{
			View::assign('alt_results', '');
		}

		View::assign('page', $page);
		View::assign('settings', $settings);
		View::assign('selection_reasons', Values::selectionReasons());
		View::assign('selected_methods', Values::selectedMethods());
		View::assign('selected_method', intval(Arr::get($settings, 'selected_method')));
		View::assign('title', A11YC_LANG_TEST_RESULT.': '.Model\Html::fetchPageTitle($url));
		View::assign('standards', Yaml::each('standards'));
		View::assign('is_center', false);
		View::assign('is_assign', $is_assign);

		// assign results
		self::assignResults($settings['target_level'], $url);

		// assign implement checklist
		View::assign('cs', Model\Checklist::fetch($url));

		// set body
		View::assign('body', View::fetchTpl('result/index.php'), false);

		return true;
	}

	/**
	 * assignResults
	 *
	 * @param Integer $target_level
	 * @param String $url
	 * @return Void
	 */
	private static function assignResults($target_level, $url = '')
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

		View::assign('cs', Model\Checklist::fetch($url));
		View::assign('is_total', empty($url));
		View::assign('setup', Model\Setting::fetchAll());
		View::assign('results', $results);
		View::assign('target_level', $target_level);
		View::assign('include', $include);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('result', View::fetchTpl('result/part_result.php'), FALSE);
	}
}
