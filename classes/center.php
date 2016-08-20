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
		\A11yc\View::assign('done', $done);
		\A11yc\View::assign('total', $total);

		// setup
		\A11yc\View::assign('setup', Setup::fetch_setup());
		\A11yc\View::assign('target_level', intval(@$setup['target_level']));
		\A11yc\View::assign('selected_method', intval(@$setup['selected_method']));

		// body
		\A11yc\View::assign('title', 'A11y Center');
		\A11yc\View::assign('body', \A11yc\View::fetch_tpl('center/index.php'), false);
	}
}
