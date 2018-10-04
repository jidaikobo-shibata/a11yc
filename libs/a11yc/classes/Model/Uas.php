<?php
/**
 * A11yc\Model\Uas
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Uas
{
	protected static $uas = null;

	/**
	 * fetch uas
	 *
	 * @param Bool $force
	 * @return Array
	 */
	public static function fetch($force = 0)
	{
		if ( ! is_null(static::$uas) && ! $force) return static::$uas;

		$sql = 'SELECT * FROM '.A11YC_TABLE_UAS.';';
		static::$uas = Db::fetchAll($sql);
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
			$r       = false;

			// id 1 is default ua. can not delete or edit str.
			$strs[1] = '';
			if (isset($deletes[1])) unset($deletes[1]);

			// exists
			$exists = array_column(static::fetch(), 'id');

			// update
			foreach ($names as $id => $name)
			{
				if ( ! in_array($id, $exists)) continue;
				$sql = 'UPDATE '.A11YC_TABLE_UAS.' SET `name` = ?, `str` = ? WHERE `id` = ?;';
				$r = Db::execute($sql, array(trim($name), trim($strs[$id]), $id));
			}

			// insert
			$name = trim(Input::post('new_name'));
			$str  = trim(Input::post('new_str'));
			if ( ! empty($name.$str))
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_UAS.' (`name`, `str`) VALUES (?, ?);';
				$r = Db::execute($sql, array($name, $str));
			}

			// delete
			foreach ($deletes as $id)
			{
				if ( ! in_array($id, $exists)) continue;
				$sql = 'DELETE FROM '.A11YC_TABLE_UAS.' WHERE `id` = ?;';
				$r = Db::execute($sql, array($id));
			}

			// message
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
}
