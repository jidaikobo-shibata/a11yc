<?php
/**
 * A11yc\Model\Settings
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Settings
{
	protected static $settings = null;

	/**
	 * fetch setup
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = 0)
	{
		if ( ! is_null(static::$settings) && ! $force) return static::$settings;

		$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS.Db::versionSql(false).';';
		$ret = Db::fetchAll($sql);
		static::$settings = Arr::get($ret, 0, array());
		return static::$settings;
	}

	/**
	 * fetch setup
	 *
	 * @param String $field
	 * @return String|Array
	 */
	public static function fetch($field, $default = '')
	{
		$settings = self::fetchAll();
		return Arr::get($settings, $field, $default);
	}

	/**
	 * dbio
	 *
	 * @return Void
	 */
	public static function dbio()
	{
		if (Input::isPostExists())
		{
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
			$base_url = rtrim(stripslashes(Input::post('base_url')), '/');

			// additional_criterions
			$additional_criterions = array();
			if (Input::postArr('additional_criterions'))
			{
				foreach (array_keys(Input::postArr('additional_criterions')) as $code)
				{
					$additional_criterions[] = $code;
				}
			}

			$settings = self::fetchAll();

			// database io
			// about unused column `trust_ssl_url` see db.php
			if (isset($settings['standard']))
			{

				// update
				$sql = 'UPDATE '.A11YC_TABLE_SETTINGS.' SET';
				$sql.= '`target_level` = ?, ';
				$sql.= '`standard` = ?, ';
				$sql.= '`selected_method` = ?, ';
				$sql.= '`declare_date` = ?, ';
				$sql.= '`test_period` = ?, ';
				$sql.= '`dependencies` = ?, ';
				$sql.= '`contact` = ?, ';
				$sql.= '`policy` = ?, ';
				$sql.= '`report` = ?,';
				$sql.= '`additional_criterions` = ?,';
				$sql.= '`base_url` = ?,';
				$sql.= '`basic_user` = ?,';
				$sql.= '`basic_pass` = ?,';
				$sql.= '`checklist_behaviour` = ?,';
				$sql.= '`stop_guzzle` = ? WHERE `version` = 0;';
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
						serialize($additional_criterions),
						$base_url,
						Input::post('basic_user', ''),
						Input::post('basic_pass', ''),
						$checklist_behaviour,
						$stop_guzzle,
					));
			}
			else
			{

				// insert
				$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS;
				$sql.= ' (`target_level`, ';
				$sql.= '`standard`, ';
				$sql.= '`selected_method`, ';
				$sql.= '`declare_date`, ';
				$sql.= '`test_period`, ';
				$sql.= '`dependencies`, ';
				$sql.= '`contact`, ';
				$sql.= '`policy`, ';
				$sql.= '`report`, ';
				$sql.= '`additional_criterions`, ';
				$sql.= '`base_url`,';
				$sql.= '`basic_user`, ';
				$sql.= '`basic_pass`, ';
				$sql.= '`checklist_behaviour`,';
				$sql.= '`stop_guzzle`,';
				$sql.= '`version`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
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
						serialize($additional_criterions),
						$base_url,
						Input::post('basic_user', ''),
						Input::post('basic_pass', ''),
						$checklist_behaviour,
						$stop_guzzle,
						0
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
	 * update field
	 *
	 * @param  String $field
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($field, $value)
	{
		if( ! self::fetch($field))
		{
			$sql = 'UPDATE '.A11YC_TABLE_SETTINGS.' SET `'.$field.'` = ?';
			$sql.= ' WHERE `version` = 0;';
		}
		else
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS.' (`'.$field.'`, `version`) ';
			$sql.= ' VALUES (?, 0);';
		}

		return Db::execute($sql, array($value));
	}

}