<?php
/**
 * A11yc\Setup
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
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
	public static function fetch_setup($force = 0)
	{
		static $retvals = '';
		if ($retvals && ! $force) return $retvals;

		$sql = 'SELECT * FROM '.A11YC_TABLE_SETUP.';';
		$ret = Db::fetch_all($sql);
		$retvals = Arr::get($ret, 0, array());
		return $retvals;
	}

	/**
	 * dbio
	 *
	 * @return  void
	 */
	public static function dbio()
	{
		if (Input::post())
		{
			$post = Input::post();
			$target_level = intval($post['target_level']);
			$selected_method = intval($post['selected_method']);
			$checklist_behaviour = intval(@$post['checklist_behaviour']);
			$standard = intval($post['standard']);
			$policy = stripslashes($post['policy']);
			$contact = stripslashes($post['contact']);
			$report = stripslashes($post['report']);

			$additional_criterions = array();
			if (Input::post('additional_criterions'))
			{
				foreach ($post['additional_criterions'] as $code => $v)
				{
					$additional_criterions[] = $code;
				}
			}

			$setup = static::fetch_setup();

			if (isset($setup['standard']))
			{
				// update

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
				$sql.= '`basic_user` = ?,';
				$sql.= '`basic_pass` = ?,';
				$sql.= '`trust_ssl_url` = ?,';
				$sql.= '`additional_criterions` = ?,';
				$sql.= '`checklist_behaviour` = ?;';
				$r = Db::execute($sql, array(
						$target_level,
						$standard,
						$selected_method,
						$post['declare_date'],
						$post['test_period'],
						$post['dependencies'],
						$contact,
						$policy,
						$report,
						$post['basic_user'],
						$post['basic_pass'],
						$post['trust_ssl_url'],
						serialize($additional_criterions),
						$checklist_behaviour
					));
			}
			else
			{
				// insert

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
				$sql.= '`basic_user`, ';
				$sql.= '`basic_pass`, ';
				$sql.= '`trust_ssl_url`, ';
				$sql.= '`additional_criterions`, ';
				$sql.= '`checklist_behaviour`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
				$r = Db::execute($sql, array(
						$target_level,
						$selected_method,
						$standard,
						$post['declare_date'],
						$post['test_period'],
						$post['dependencies'],
						$contact,
						$policy,
						$report,
						$post['basic_user'],
						$post['basic_pass'],
						$post['trust_ssl_url'],
						serialize($additional_criterions),
						$checklist_behaviour
					));
			}
			if ($r)
			{
				Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
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

		$setup = static::fetch_setup($force = 1);

		// assign
		View::assign('title', A11YC_LANG_SETUP_TITLE);
		View::assign('setup', $setup);
		View::assign('standards', Yaml::each('standards'));
		View::assign('yml', Yaml::fetch());
		View::assign('form', View::fetch_tpl('setup/form.php'), FALSE);
	}
}