<?php
/**
 * A11yc\Setup
 *
 * @package    part of A11yc
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
	 * @return Void
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
	 * @return Array
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
	 * get selection methods
	 *
	 * @return Array
	 */
	public static function selected_methods()
	{
		return array(
			0 => A11YC_LANG_CANDIDATES0,
			1 => A11YC_LANG_CANDIDATES1,
			2 => A11YC_LANG_CANDIDATES2,
			3 => A11YC_LANG_CANDIDATES3,
			4 => A11YC_LANG_CANDIDATES4,
		);
	}

	/**
	 * get additional criterions
	 *
	 * @return Array
	 */
	public static function additional_criterions()
	{
		$setup = static::fetch_setup();
		if(isset($setup['additional_criterions']) && $setup['additional_criterions'])
		{
			$str = str_replace('&quot;', '"', $setup['additional_criterions']);
			return unserialize($str);
		}
		return array();
	}

	/**
	 * dbio
	 *
	 * @return Void
	 */
	public static function dbio()
	{
		if (Input::is_post_exists())
		{
			// protect data
			if (Input::post('protect_data'))
			{
				$path = A11YC_DATA_PATH.A11YC_DATA_FILE;
				$file = A11YC_DATA_PATH.Controller_Disclosure::version2filename(date('Ymd'), $force = TRUE);

				if (file_exists($file))
				{
					unlink($file);
					Session::add('messages', 'messages', A11YC_LANG_DISCLOSURE_DELETE_SAMEDATE);
				}

				// protect
				if (copy($path, $file))
				{
					Session::add('messages', 'messages', A11YC_LANG_DISCLOSURE_PROTECT_DATA_SAVED);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_DISCLOSURE_PROTECT_DATA_FAILD);
				}
				return;
			}

			// setup
			$target_level = intval(Input::post('target_level'));
			$selected_method = intval(Input::post('selected_method'));
			$checklist_behaviour = intval(Input::post('checklist_behaviour'));
			$stop_guzzle = intval(Input::post('stop_guzzle'));
			$standard = intval(Input::post('standard'));

			// stripslashes
			$declare_date = stripslashes(Input::post('declare_date'));
			$test_period = stripslashes(Input::post('test_period'));
			$dependencies = stripslashes(Input::post('dependencies'));
			$policy = stripslashes(Input::post('policy'));
			$report = stripslashes(Input::post('report'));
			$contact = stripslashes(Input::post('contact'));

			// additional_criterions
			$additional_criterions = array();
			if (Input::post_arr('additional_criterions'))
			{
				foreach (array_keys(Input::post_arr('additional_criterions')) as $code)
				{
					$additional_criterions[] = $code;
				}
			}

			$setup = static::fetch_setup();

			// database io
			// about unused column `trust_ssl_url` see db.php
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
				$sql.= '`checklist_behaviour` = ?,';
				$sql.= '`stop_guzzle` = ?;';
				$r = Db::execute($sql, array(
						$target_level,
						$standard,
						$selected_method,
						$declare_date,
						$test_period,
						$dependencies,
						$contact,
						$policy,
						$report,
						Input::post('basic_user', ''),
						Input::post('basic_pass', ''),
						'',
						serialize($additional_criterions),
						$checklist_behaviour,
						$stop_guzzle
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
				$sql.= '`checklist_behaviour`,';
				$sql.= '`stop_guzzle`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
				$r = Db::execute($sql, array(
						$target_level,
						$standard,
						$selected_method,
						$declare_date,
						$test_period,
						$dependencies,
						$contact,
						$policy,
						$report,
						Input::post('basic_user', ''),
						Input::post('basic_pass', ''),
						'',
						serialize($additional_criterions),
						$checklist_behaviour,
						$stop_guzzle
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
	 * @return Void
	 */
	public static function form()
	{
		// current setup
		static::dbio();

		$setup = static::fetch_setup($force = 1);

		// assign
		View::assign('sample_policy', str_replace("\\n", "\n", A11YC_LANG_SAMPLE_POLICY));
		View::assign('selected_methods', static::selected_methods());
		View::assign('title', A11YC_LANG_SETUP_TITLE);
		View::assign('setup', $setup);
		View::assign('standards', Yaml::each('standards'));
		View::assign('yml', Yaml::fetch());
		View::assign('form', View::fetch_tpl('setup/form.php'), FALSE);
	}
}