<?php
/**
 * A11yc\Disclosure
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Controller_Disclosure
{
	/**
	 * Show Total Results
	 *
	 * @return  void
	 */
	public static function total()
	{
		// setup
		$setup = Controller_Setup::fetch_setup();
		if ( ! $setup['policy']) die('Error. Set policy first');
		$target_level = intval(@$setup['target_level']);
		if ( ! $target_level) die('Error. Set target level first');

		// page list
		if (isset($_GET['a11yc_pages']))
		{
			$pages = Db::fetch_all('SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `trash` = 0 AND `done` = 1 ORDER BY `url` ASC;');
			View::assign('pages', $pages);
			View::assign('title', A11YC_LANG_CHECKED_PAGES);
			View::assign('body', View::fetch_tpl('disclosure/pages.php'), false);
		}

		// report
		else if (isset($_GET['a11yc_report']))
		{
			// assign common values for total report
			Controller_Center::index();
			View::assign('is_total', TRUE);
			View::assign('title', A11YC_LANG_TEST_RESULT);
			View::assign('body', View::fetch_tpl('disclosure/index.php'), false);
		}

		// each report
		else if (isset($_GET['url']))
		{
			static::each($_GET['url']);
		}

		// policy
		else
		{
			View::assign('title', A11YC_LANG_POLICY);
			$url = \A11yc\Util::remove_query_strings(
				\A11yc\Util::uri(),
				array('a11yc_policy')
			);
			$url = \A11yc\Util::add_query_strings(
				$url,
				array(
					array('a11yc_report', 1)
				));
			$policy = $setup['policy'];
			$policy.= '<h2>'.A11YC_LANG_REPORT."</h2>\n";
			$policy.= '<p class="a11yc_link"><a href="'.$url.'">'.A11YC_LANG_REPORT."</a></p>";
			View::assign('body', $policy, false);
		}
	}

	/**
	 * Show each page report
	 *
	 * @return  void
	 */
	public static function each($url)
	{
		// page
		$page = Controller_Checklist::fetch_page($url);
		if ( ! $page || ! $page['done'])
		{
			Session::add('messages', 'errors', array(A11YC_LANG_PAGES_NOT_FOUND));
			header("location:javascript://history.go(-1)");
			exit();
		}
		View::assign('page', Controller_Checklist::fetch_page($url));

		// setup
		$setup = Controller_Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);
		if ( ! $target_level) die('Error. Set target level first');

		View::assign('setup', $setup);
		View::assign('target_level', $target_level);
		View::assign('selected_method', intval(@$setup['selected_method']));
		View::assign('is_total', FALSE);

		// result
		list($results, $checked, $passed_flat) = Evaluate::evaluate_url($url);
		Controller_Checklist::part_result($results, $target_level);
		$result = \A11yc\View::fetch('result');

		$additional = '';
		if ($target_level != 3)
		{
			Controller_Checklist::part_result($results, $target_level, false);
			$additional = \A11yc\View::fetch('result');
		}

		View::assign('result', $result, false);
		View::assign('additional', $additional, false);

		// body
		\A11yc\View::assign('title', A11YC_LANG_TEST_RESULT.': '.Util::fetch_page_title($url));
		View::assign('body', View::fetch_tpl('disclosure/index.php'), false);
	}
}
