<?php
/**
 * A11yc\Disclosure
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Controller_Disclosure
{
	/**
	 * links
	 *
	 * @return  void
	 */
	public static function assign_links()
	{
		$url = \A11yc\Util::remove_query_strings(
			\A11yc\Util::uri(),
			array('a11yc_policy', 'a11yc_report', 'a11yc_pages', 'url')
		);

		$policy_link = $url;
		$report_link = \A11yc\Util::add_query_strings(
			$url,
			array(
				array('a11yc_report', 1)
			));
		$pages_link = \A11yc\Util::add_query_strings(
			$url,
			array(
				array('a11yc_pages', 1)
			));

		View::assign('policy_link', $policy_link);
		View::assign('report_link', $report_link);
		View::assign('pages_link', $pages_link);
	}

	/**
	 * Show Results index
	 *
	 * @return  void
	 */
	public static function index()
	{
		// setup
		$setup = Controller_Setup::fetch_setup();
		if ( ! $setup['target_level']) Util::error(A11YC_LANG_ERROR_NON_TARGET_LEVEL);
		$target_level = intval(@$setup['target_level']);
		View::assign('setup', $setup);
		View::assign('is_center', FALSE);

		// assign links
		static::assign_links();

		// page list
		if (Input::get('a11yc_pages'))
		{
			foreach (array_keys(Controller_Checklist::selection_reasons()) as $k)
			{
				$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0 AND `done` = 1';
				if ($k <= 5)
				{
					$sql.= ' AND `selection_reason` = ? ORDER BY `url` ASC;';
					$pages[$k] = Db::fetch_all($sql, array($k));
				}
				else
				{
					$sql.= ' AND `selection_reason` = 6 OR `selection_reason` = 0';
					$sql.= ' OR `selection_reason` is null ORDER BY `url` ASC;';
					$pages[$k] = Db::fetch_all($sql);
				}
			}
			View::assign('selection_reasons', Controller_Checklist::selection_reasons());
			View::assign('pages', $pages);
			View::assign('title', A11YC_LANG_CHECKED_PAGES);
			View::assign('body', View::fetch_tpl('disclosure/pages.php'), false);
			return;
		}

		// report
		else if (Input::get('a11yc_report') || Input::get('url'))
		{
			static::report(Input::get('url', ''));
			return;
		}

		// policy
		View::assign('policy', $setup['policy']);
		View::assign('report_link', $report_link);
		View::assign('title', A11YC_LANG_POLICY);
		View::assign('body', View::fetch_tpl('disclosure/policy.php'), false);
		return;
	}

	/**
	 * Show report
	 *
	 * @return  void
	 */
	public static function report($url = '')
	{
		$is_total = FALSE;
		// report of each page
		if ($url)
		{
			$page = Controller_Pages::fetch_page($url);
			if ( ! $page || ! $page['done'])
			{
				Session::add('messages', 'errors', array(A11YC_LANG_PAGES_NOT_FOUND));
				header("location:javascript://history.go(-1)");
				exit();
			}
			View::assign('page', $page);
		}
		// total report
		else
		{
			$is_total = TRUE;
			// count
			$sql = 'SELECT count(`url`) as num FROM '.A11YC_TABLE_PAGES.' WHERE ';
			$done =  $sql.' `done` = 1 and `trash` = 0;';
			$total = $sql.' `trash` <> 1;';
			View::assign('done', Db::fetch($done));
			View::assign('total', Db::fetch($total));
		}

		// setup
		$setup = Controller_Setup::fetch_setup();
		$target_level = intval(Arr::get($setup, 'target_level'));
		if ( ! $target_level) Util::error('Error. Set target level first');

		static::assign_links();
		View::assign('setup', $setup);
		View::assign('target_level', $target_level);
		View::assign('selection_reasons', Controller_Checklist::selection_reasons());
		View::assign('selected_methods', Controller_Setup::selected_methods());
		View::assign('selected_method', intval(Arr::get($setup, 'selected_method')));
		View::assign('is_total', $is_total);

		// results
		if ($is_total)
		{
			// passed and unpassed pages
			View::assign('unpassed_pages', \A11yc\Evaluate::unpassed_pages($target_level));
			View::assign('passed_pages', \A11yc\Evaluate::passed_pages($target_level));

			$results = Evaluate::evaluate_total();
			View::assign('title', A11YC_LANG_TEST_RESULT);
		}
		else
		{
			$results = Evaluate::evaluate_url($url);
			View::assign('title', A11YC_LANG_TEST_RESULT.': '.Util::fetch_page_title($url));
		}

		// result - target level
		Controller_Checklist::part_result($results, $target_level);
		$result = \A11yc\View::fetch('result');

		// result - additional level
		$additional = '';
		if ($target_level != 3)
		{
			Controller_Checklist::part_result($results, $target_level, false);
			$additional = \A11yc\View::fetch('result');
		}

		View::assign('result', $result, false);
		View::assign('additional', $additional, false);

		// set body
		View::assign('body', View::fetch_tpl('disclosure/index.php'), false);
	}
}
