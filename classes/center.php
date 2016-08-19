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
		$setup = Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);
		$selected_method = intval(@$setup['selected_method']);

		$html = '';

		// target level
		$html.= '<h2>'.A11YC_LANG_TARGET_LEVEL.'</h2>';
		$html.= '<p>'.Util::num2str($target_level).'</p>';

		// current level
		$html.= '<h2>'.A11YC_LANG_CURRENT_LEVEL_WEBPAGES.'</h2>';
		$site_level = Evaluate::check_site_level();
		$html.= Evaluate::result_str($site_level, $target_level);

		// selected method
		$html.= '<h2>'.A11YC_LANG_CANDIDATES0.'</h2>';
		$arr = array(
			A11YC_LANG_CANDIDATES1,
			A11YC_LANG_CANDIDATES2,
			A11YC_LANG_CANDIDATES3,
			A11YC_LANG_CANDIDATES4,
		);
		$html.= '<p>'.$arr[$selected_method].'</p>';

		// number of checked
		$html.= '<h2>'.A11YC_LANG_NUM_OF_CHECKED.'</h2>';
		$done = Db::fetch('SELECT count(`level`) as done FROM '.A11YC_TABLE_PAGES.' WHERE `done` = 1 and `trash` = 0;');
		$total = Db::fetch('SELECT count(`level`) as total FROM '.A11YC_TABLE_PAGES.' WHERE `trash` <> 1;');
		$html.= '<p>'.$done['done'].' / '.$total['total'].'</p>';

		// unpassed pages
		$html.= '<h2>'.A11YC_LANG_UNPASSED_PAGES.'</h2>';
		$unpassed_pages = Evaluate::unpassed_pages($target_level);
		if ($unpassed_pages)
		{
			$html.= '<ul>';
			foreach ($unpassed_pages as $v)
			{
				$url = htmlspecialchars($v['url'], ENT_QUOTES);
				$html.= '<li>';
				$html.= '<a href="'.$url.'"'.A11YC_TARGET.'>'.$url.'</a>';
				$html.= ' (<a href="'.A11YC_CHECKLIST_URL.$url.'"'.A11YC_TARGET.'>check</a>)';
				$html.= '</li>';
			}
			$html.= '<ul>';
		}
		else
		{
			$html.= '<p>'.A11YC_LANG_UNPASSED_PAGES_NO.'</p>';
		}

		// site results
		list($results, $checked, $passed_flat) = Evaluate::evaluate(Evaluate::evaluate_total());
		$html.= '<h2>'.A11YC_LANG_CHECKLIST_TITLE.'</h2>';
		$html.= Checklist::part_result($results, $target_level);

		return array('A11y Center', $html);
	}
}
