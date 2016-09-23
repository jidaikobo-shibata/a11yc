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
class Controller_Setup
{
	/**
	 * action
	 *
	 * @return  void
	 */
	public static function Action_Index()
	{
		static::form();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetch_tpl('setup/index.php'), false);
	}

	/**
	 * fetch setup
	 *
	 * @return  array
	 */
	public static function fetch_setup()
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_SETUP.';';
		$ret = Db::fetch_all($sql);
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
			$post = $_POST;
			$target_level = intval($post['target_level']);
			$selected_method = intval($post['selected_method']);
			$checklist_behaviour = intval(@$post['checklist_behaviour']);
			$standard = intval($post['standard']);

			$setup = static::fetch_setup();

			if (isset($setup['report']))
			{
				$sql = 'UPDATE '.A11YC_TABLE_SETUP.' SET';
				$sql.= '`target_level` = ?, ';
				$sql.= '`standard` = ?, ';
				$sql.= '`selected_method` = ?, ';
				$sql.= '`declare_date` = ?, ';
				$sql.= '`test_period` = ?, ';
				$sql.= '`dependencies` = ?, ';
				$sql.= '`contact` = ?, ';
				$sql.= '`policy` = ?, ';
				$sql.= '`report` = ?,';
				$sql.= '`checklist_behaviour` = ?;';
				$r = Db::execute($sql, array(
						$target_level,
						$standard,
						$selected_method,
						$post['declare_date'],
						$post['test_period'],
						$post['dependencies'],
						$post['contact'],
						$post['policy'],
						$post['report'],
						$checklist_behaviour
					));
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
				$sql.= '`checklist_behaviour`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
				$r = Db::execute($sql, array(
						$target_level,
						$selected_method,
						$standard,
						$post['declare_date'],
						$post['test_period'],
						$post['dependencies'],
						$post['contact'],
						$post['policy'],
						$post['report'],
						$checklist_behaviour
					));
			}
			if ($r)
			{
				\A11yc\View::assign('messages', array(A11YC_LANG_UPDATE_SUCCEED));
			}
			else
			{
				\A11yc\View::assign('errors', array(A11YC_LANG_UPDATE_FAILED));
			}
		}
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