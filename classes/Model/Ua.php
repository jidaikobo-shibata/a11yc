<?php
/**
 * A11yc\Model\Ua
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Ua
{
	protected static $uas = null;

	/**
	 * fetch uas
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($force = false)
	{
		if ( ! is_null(static::$uas) && ! $force) return static::$uas;
		$vals = Setting::fetch('user_agents', array(), $force);
		static::$uas = is_array($vals) ? $vals : array();
		return static::$uas;
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
			$names   = Input::postArr('name');
			$strs    = Input::postArr('str');
			$deletes = Input::postArr('delete');

			// id 1 is default ua. can not delete or edit str.
			$strs[1] = '';
			if (isset($deletes[1])) unset($deletes[1]);

			$value = array();
			foreach ($names as $id => $v)
			{
				if (in_array($id, $deletes)) continue;
				$value[$id]['id'] = $id;
				$value[$id]['name'] = $names[$id];
				$value[$id]['str'] = $strs[$id];
			}

			$name = trim(Input::post('new_name'));
			$str  = trim(Input::post('new_str'));
			if ( ! empty($name.$str))
			{
				$id = max(array_keys($value)) + 1;
				$value[$id]['id'] = $id;
				$value[$id]['name'] = $name;
				$value[$id]['str'] = $str;
			}

			// message
			if (Setting::update('user_agents', $value) === true)
			{
				Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
			}
		}
	}
}
