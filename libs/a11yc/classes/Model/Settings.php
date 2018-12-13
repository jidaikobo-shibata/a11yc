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
	protected static $serializeds = array(
		'additional_criterions',
		'non_exist_and_passed_criterions',
		'non_use_techs',
	);

	/**
	 * fetch setup
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetchAll($force = false)
	{
		if ( ! is_null(static::$settings) && ! $force) return static::$settings;

		$sql = 'SELECT * FROM '.A11YC_TABLE_SETTINGS.Db::versionSql(false).';';
		$ret = Db::fetchAll($sql);

		// array_column()...
		if (version_compare(phpversion(), '5.5') >= 0)
		{
			static::$settings = array_column($ret, 'value', 'key');
		}
		else
		{
			foreach ($ret as $v)
			{
				static::$settings[$v['key']] = $v['value'];
			}
		}

		// serialized values
		foreach (static::$serializeds as $serialized)
		{
			static::$settings[$serialized] = isset(static::$settings[$serialized]) ?
																		 Util::unquoteUnserialize(static::$settings[$serialized]) :
																		 array();
		}

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
			$intvals = array(
				'target_level',
				'selected_method',
				'stop_guzzle',
				'standard',
				'show_results',
			);
			$cols = array();
			foreach ($intvals as $v)
			{
				$cols[$v] = intval(Input::post($v, 0));
			}

			// stripslashes
			$stripslashes =array(
				'client_name',
				'declare_date',
				'test_period',
				'dependencies',
				'policy',
				'report',
				'contact',
				'base_url',
				'basic_user',
				'basic_pass',
			);
			foreach ($stripslashes as $v)
			{
				$cols[$v] = stripslashes(Input::post($v, ''));
			}
			$cols['base_url'] = rtrim($cols['base_url'], '/');

			// serialized values
			$serializeds = array(
				'additional_criterions',
				'non_exist_and_passed_criterions',
				'non_use_techs',
			);
			foreach ($serializeds as $serialized)
			{
				$values = array();
				if (Input::postArr($serialized))
				{
					foreach (array_keys(Input::postArr($serialized)) as $code)
					{
						$values[] = $code;
					}
				}
				$cols[$serialized] = serialize($values);
			}

			// database io
			$r = true;
			foreach ($cols as $key => $value)
			{
				$r = self::updateField($key, $value);
				if ( ! $r) continue;
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
	 * @param  String $key
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($key, $value)
	{
		$settings = self::fetchAll();
		if( ! isset($settings[$key]))
		{
			$sql = 'INSERT INTO '.A11YC_TABLE_SETTINGS.' (`key`, `value`, `version`) ';
			$sql.= ' VALUES (?, ?, 0);';
			$r = Db::execute($sql, array($key, $value));
		}
		else
		{
			$sql = 'UPDATE '.A11YC_TABLE_SETTINGS.' SET `value` = ?';
			$sql.= ' WHERE `key` = ? AND `version` = 0;';
			$r = Db::execute($sql, array($value, $key));
		}

		return $r;
	}

}
