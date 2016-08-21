<?php
/**
 * A11yc\Center
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Center
{
	/**
	 * Show A11y Center Index
	 *
	 * @return  void
	 */
	public static function index()
	{
		// count
		$done = Db::fetch('SELECT count(`level`) as done FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0;');
		$total = Db::fetch('SELECT count(`level`) as total FROM '.A11YC_TABLE_PAGES.' WHERE `trash` <> 1;');
		View::assign('done', $done);
		View::assign('total', $total);

		// setup
		$setup = Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);
		View::assign('setup', $setup);
		View::assign('target_level', $target_level);
		View::assign('selected_method', intval(@$setup['selected_method']));

		// result
		list($results, $checked, $passed_flat) = Evaluate::evaluate(Evaluate::evaluate_total());
		Checklist::part_result($results, $target_level);

		// body
		View::assign('title', A11YC_LANG_CENTER_TITLE);
		View::assign('body', View::fetch_tpl('center/index.php'), false);
	}
}
