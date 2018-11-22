<?php
/**
 * A11yc\Controller\Results
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Results
{
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
	 * links
	 *
	 * @return Void
	 */
	public static function assignLinks()
	{
		$url = Util::removeQueryStrings(
			Util::uri(),
			array('a11yc_policy', 'a11yc_report', 'a11yc_pages', 'url')
		);

		// base page
		$results_link = Util::removeQueryStrings(
			$url,
			array('a11yc_version')
		);

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
	 * @param  String $target_level
	 * @param  String $level
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
	 * @return Void
	 */
	public static function each($url)
	{
		$page = Model\Pages::fetchPage($url);
		if ( ! $page || ! $page['done'])
		{
			Session::add('messages', 'errors', A11YC_LANG_PAGES_NOT_FOUND);
			header("location:javascript://history.go(-1)");
			exit();
		}

		$settings = Model\Settings::fetchAll();
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

		// assign results
		self::assignResults($settings['target_level'], $url);

		// set body
		View::assign('body', View::fetchTpl('results/index.php'), false);
	}

	/**
	 * Show report
	 *
	 * @param  Bool $is_center
	 * @return Void
	 */
	public static function all($is_center = false)
	{
		$settings = Model\Settings::fetchAll();
		$target_level = intval(Arr::get($settings, 'target_level'));

		static::assignLinks();
		self::assignLevels($target_level);

		View::assign('settings',          $settings);
		View::assign('target_level',      $target_level);
		View::assign('selection_reasons', Values::selectionReasons());
		View::assign('selected_methods',  Values::selectedMethods());
		View::assign('selected_method',   intval(Arr::get($settings, 'selected_method')));
		View::assign('done',              Model\Pages::count('done'));
		View::assign('total',             Model\Pages::count('all'));
		View::assign('standards',         Yaml::each('standards'));
		View::assign('is_center',         $is_center);
		View::assign('title',             A11YC_LANG_TEST_RESULT);

		// passed and unpassed pages
		View::assign('unpassed_pages', Model\Results::unpassedPages($target_level));
		View::assign('passed_pages',   Model\Results::passedPages($target_level));

		// assign result
		self::assignResults($target_level);

		// set body
		View::assign('body', View::fetchTpl('results/index.php'), false);
	}

	/**
	 * Show pages
	 *
	 * @return Void
	 */
	public static function pages()
	{
		$args = array(
			'list'   => 'done',
			'order'  => Input::get('order', 'url_asc'),
		);

		$pdfs = array();
		$pages = array();
		foreach (array_keys(Values::selectionReasons()) as $k)
		{
			$args['reason'] = $k;
			$args['type']   = 'html';
			$pages[$k] = Model\Pages::fetch($args);

			$args['type'] = 'pdf';
			$pdfs = array_merge($pdfs, Model\Pages::fetch($args));
		}
		ksort($pages);

		$pages['pdf'] = $pdfs;

		// assign links
		static::assignLinks();

		View::assign('settings', Model\Settings::fetchAll());
		View::assign('selection_reasons', Values::selectionReasons());
		View::assign('pages', $pages);
		View::assign('title', A11YC_LANG_CHECKED_PAGES);
		View::assign('body', View::fetchTpl('results/pages.php'), false);
		return;
	}

	/**
	 * Show Results index
	 *
	 * @return Void
	 */
	public static function index()
	{
		// settings
		$settings = Model\Settings::fetchAll();
		View::assign('settings', $settings);
		View::assign('is_center', FALSE);

		// assign links
		static::assignLinks();

		// page list
		if (Input::get('a11yc_pages') && $settings['show_results'])
		{
			static::pages();
			return;
		}
		// each report
		else if ($settings['show_results'] && Input::get('url'))
		{
			static::each(Input::get('url', ''));
			return;
		}
		// report
		else if ($settings['show_results'] && Input::get('a11yc_report'))
		{
			static::all();
			return;
		}

		// assign results
		View::assign('is_policy', true);
		self::assignResults($settings['target_level']);

		// policy
		View::assign('versions', Model\Versions::fetch());
		View::assign('policy', $settings['policy'], false);
		View::assign('title', A11YC_LANG_POLICY);
		View::assign('body', View::fetchTpl('results/policy.php'), false);
		return;
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
	 * @param  Array   $results
	 * @param  Integer $target_level
	 * @param  String  $url
	 * @param  Bool    $include
	 * @return Void
	 */
	private static function partResult($results, $target_level, $url = '', $include = TRUE)
	{
		View::assign(
			'non_exist_and_passed_criterions',
			Model\Settings::fetch('non_exist_and_passed_criterions')
		);

		View::assign('cs', Model\Checklist::fetch($url));
		View::assign('is_total', empty($url));
		View::assign('setup', Model\Settings::fetchAll());
		View::assign('results', $results);
		View::assign('target_level', $target_level);
		View::assign('include', $include);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('result', View::fetchTpl('results/part_result.php'), FALSE);
	}

	/**
	 * implements Checklist
	 *
	 * @return Void
	 */
	public static function implementsChecklist()
	{
		$errors = Yaml::each('errors');

		$checks = array();
		foreach ($errors as $k => $v)
		{
			if (Arr::get($v, 'not4checklist') == true) continue;
			foreach ($v['criterions'] as $criterion)
			{
				if ( ! isset($checks[$criterion])) $checks[$criterion] = array();
				$techs = Arr::get($v, 'techs', array());

				foreach ($techs as $kk => $vv)
				{
					// remove F
					if ($vv[0] == 'F') unset($techs[$kk]);
				}

				$checks[$criterion][$k] = array(
					'title' => $v['title'],
					'techs' => $techs
				);
			}
		}
		ksort($checks);

		// tech_codes
		$tech_codes = array();
		$tech_codes_origi = Yaml::each('techs_codes');
		foreach ($tech_codes_origi as $criterion => $v)
		{
			$v['t'] = array_diff($v['t'], Model\Settings::fetch('non_use_techs'));
			$tech_codes[$criterion] = $v['t'];
		}

		View::assign('implements', $checks);
		View::assign('errors', $errors);
		View::assign('criterions', Yaml::each('criterions'));
		View::assign('techs_codes', $tech_codes);
		View::assign('techs', Yaml::each('techs'));
		View::assign(
			'implements_checklist',
			View::fetchTpl('results/implements_checklist.php'),
			FALSE
		);
	}
}
