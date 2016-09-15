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
		$ret = \A11yc\Db::fetch_all($sql);
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
			$esc_post = Db::escape($_POST);
			$target_level = intval($_POST['target_level']);
			$selected_method = intval($_POST['selected_method']);
			$checklist_behaviour = intval(@$_POST['checklist_behaviour']);
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
		static::form();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetch_tpl('setup/index.php'), false);
	}

	/**
	 * setup form
	 *
	 * @return  string
	 */
	public static function form()
	{
		static::dbio();

		// assign
		View::assign('title', A11YC_LANG_SETUP_TITLE);
		View::assign('setup', static::fetch_setup());
		View::assign('standards', Yaml::each('standards'));
		View::assign('form', View::fetch_tpl('setup/form.php'), FALSE);
	}
}