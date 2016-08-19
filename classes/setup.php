<?php
/**
 * A11yc\Setup
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Setup
{
	/**
	 * fetch setup
	 *
	 * @return  array
	 */
	public static function fetch_setup()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_SETUP.';';
		$ret = \A11yc\Db::fetchAll($sql);
		return isset($ret[0]) ? $ret[0] : array();
	}

	/**
	 * dbio
	 *
	 * @return  void
	 */
	public static function dbio()
	{
		if (isset($_POST) && $_POST)
		{
			$esc_post = Db::escapeStr($_POST);
			$target_level = intval($_POST['target_level']);
			$selected_method = intval($_POST['selected_method']);
			$checklist_behaviour = intval($_POST['checklist_behaviour']);
			$standard = intval($_POST['standard']);

			$setup = static::fetch_setup();

			if (isset($setup['report']))
			{
				$sql = 'UPDATE '.A11YC_TABLE_SETUP.' SET';
				$sql.= '`target_level` = '.$target_level.', ';
				$sql.= '`standard` = '.$standard.', ';
				$sql.= '`selected_method` = '.$selected_method.', ';
				$sql.= '`declare_date` = '.$esc_post['declare_date'].', ';
				$sql.= '`test_period` = '.$esc_post['test_period'].', ';
				$sql.= '`dependencies` = '.$esc_post['dependencies'].', ';
				$sql.= '`contact` = '.$esc_post['contact'].', ';
				$sql.= '`policy` = '.$esc_post['policy'].', ';
				$sql.= '`report` = '.$esc_post['report'].',';
				$sql.= '`checklist_behaviour` = '.$checklist_behaviour.';';
			}
			else
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_SETUP;
				$sql.= ' (`target_level`, ';
				$sql.= '`standard`, ';
				$sql.= '`selected_method`, ';
				$sql.= '`declare_date`, ';
				$sql.= '`test_period`, ';
				$sql.= '`dependencies`, ';
				$sql.= '`contact`, ';
				$sql.= '`policy`, ';
				$sql.= '`report`, ';
				$sql.= '`checklist_behaviour`) VALUES (';
				$sql.= $target_level.', ';
				$sql.= $selected_method.', ';
				$sql.= $standard.', ';
				$sql.= $esc_post['declare_date'].', ';
				$sql.= $esc_post['test_period'].', ';
				$sql.= $esc_post['dependencies'].', ';
				$sql.= $esc_post['contact'].', ';
				$sql.= $esc_post['policy'].', ';
				$sql.= $esc_post['report'].', ';
				$sql.= $checklist_behaviour.');';
			}
			Db::execute($sql);
		}
	}

	/**
	 * index
	 *
	 * @return  string
	 */
	public static function index()
	{
		$html = '';
		$html.= '<form action="" method="POST">';
		$html.= static::form();
		$html.= '<input type="submit" value="submit" />';
		$html.= '</form>';
		return array('', $html);
	}

	/**
	 * setup form
	 *
	 * @return  string
	 */
	public static function form()
	{
		static::dbio();

		$setup = static::fetch_setup();
		$setup = Util::s($setup);
		$standards = Yaml::each('standards');

		$html = '';
		$html.= '<h2>'.A11YC_LANG_SETUP_TITLE.'</h2>';
		$html.= '<h3><label for="a11yc_declare_date">'.A11YC_LANG_DECLARE_DATE.'</label></h3>';
		$html.= '<div><input type="text" name="declare_date" id="a11yc_declare_date" size="10" value="'.@$setup['declare_date'].'"></div>';

		$html.= '<h2><label for="a11yc_standard">'.A11YC_LANG_STANDARD.'</label></h2>';
		$html.= '<div><select name="standard" id="a11yc_standard">';
		foreach ($standards['standards'] as $k => $v)
		{
			$selected = $k == $setup['standard'] ? ' selected="selected"' : '';
			$html.= '<option'.$selected.' value="'.$k.'">'.$v.'</option>';
		}
		$html.= '</select></div>';

		$html.= '<h3><label for="a11yc_target_level">'.A11YC_LANG_TARGET_LEVEL.'</label></h3>';
		$html.= '<div><select name="target_level" id="a11yc_target_level">';
		foreach (array('', 'A', 'AA', 'AAA') as $k => $v)
		{
			$selected = @$setup['target_level'] == $k ? ' selected="selected"' : '';
			$html.= '<option'.$selected.' value="'.$k.'">'.$v.'</option>';
		}
		$html.= '</select></div>';

		$html.= '<h3><label for="a11yc_selected_method">'.A11YC_LANG_CANDIDATES0.'</label></h3>';
		$html.= '<div><select name="selected_method" id="a11yc_selected_method">';
		$selected_methods = array(
			A11YC_LANG_CANDIDATES1,
			A11YC_LANG_CANDIDATES2,
			A11YC_LANG_CANDIDATES3,
			A11YC_LANG_CANDIDATES4,
		);
		foreach ($selected_methods as $k => $v)
		{
			$selected = @$setup['selected_method'] == $k ? ' selected="selected"' : '';
			$html.= '<option'.$selected.' value="'.$k.'">'.$v.'</option>';
		}
		$html.= '</select></div>';

		$html.= '<h3><label for="a11yc_test_period">'.A11YC_LANG_TEST_PERIOD.'</label></h3>';
		$html.= '<div><input type="text" name="test_period" id="a11yc_test_period" size="20" value="'.htmlspecialchars(@$setup['test_period'], ENT_QUOTES).'"></div>';

		$html.= '<h3><label for="a11yc_dependencies">'.A11YC_LANG_DEPENDENCIES.'</label></h3>';
		$html.= '<div><textarea name="dependencies" id="a11yc_dependencies" style="width:100%;" rows="7">'.@$setup['dependencies'].'</textarea></div>';

		$html.= '<h3><label for="a11yc_policy">'.A11YC_LANG_POLICY.'</label></h3>';
		$html.= '<p>'.A11YC_LANG_POLICY_DESC.'</p>';
		$html.= '<div><textarea name="policy" id="a11yc_policy" style="width:100%;" rows="7">'.@$setup['policy'].'</textarea></div>';

		$html.= '<h3><label for="a11yc_report">'.A11YC_LANG_REPORT.'</label></h3>';
		$html.= '<p>'.A11YC_LANG_REPORT_DESC.'</p>';
		$html.= '<div><textarea name="report" id="a11yc_report" style="width:100%;" rows="7">'.@$setup['report'].'</textarea></div>';

		$html.= '<h3><label for="a11yc_contact">'.A11YC_LANG_CONTACT.'</label></h3>';
		$html.= '<p>'.A11YC_LANG_CONTACT_DESC.'</p>';
		$html.= '<div><textarea name="contact" id="a11yc_contact" style="width:100%;" rows="7">'.@$setup['contact'].'</textarea></div>';

		$html.= '<h2>'.A11YC_LANG_SETUP_TITLE_ETC.'</h2>';
		$checked = @$setup['checklist_behaviour'] ? ' checked="checked"' : '';
		$html.= '<div><label for="a11yc_checklist_behaviour"><input type="checkbox" name="checklist_behaviour" id="a11yc_checklist_behaviour" value="1"'.$checked.' />'.A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR.'</label></div>';

		return $html;
	}
}