<?php
/**
 * A11yc\Center
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Controller_Center
{
	/**
	 * action
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		static::index();
	}

	/**
	 * Show A11y Center Index
	 *
	 * @return  void
	 */
	public static function index()
	{
		// count
		$done = Db::fetch('SELECT count(`url`) as done FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0;');
		$total = Db::fetch('SELECT count(`url`) as total FROM '.A11YC_TABLE_PAGES.' WHERE `trash` <> 1;');
		View::assign('done', $done);
		View::assign('total', $total);

		// setup
		$setup = Controller_Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);
		View::assign('setup', $setup, false);
		View::assign('target_level', $target_level);
		View::assign('selected_method', intval(@$setup['selected_method']));

		// result
		list($results, $checked, $passed_flat) = Evaluate::evaluate(Evaluate::evaluate_total());
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

		// passed and unpassed pages
		View::assign('unpassed_pages', \A11yc\Evaluate::unpassed_pages($target_level));
		View::assign('passed_pages', \A11yc\Evaluate::passed_pages($target_level));

		// body
		View::assign('title', A11YC_LANG_CENTER_TITLE);
		View::assign('body', View::fetch_tpl('center/index.php'), false);
	}
}
