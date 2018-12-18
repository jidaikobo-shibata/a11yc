<?php
/**
 * A11yc\Model\Issuesbbs
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Model;

class Issuesbbs
{
	/**
	 * fetch
	 *
	 * @param  Integer $issue_id
	 * @return Array
	 */
	public static function fetchAll($issue_id)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUESBBS.' WHERE `issue_id` = ?;';
		return Db::fetchAll($sql, array($issue_id));
	}

	/**
	 * insert each message
	 *
	 * @param  Array $vals
	 * @return Integer|Bool
	 */
	public static function insert($vals)
	{
		$issue_id   = Arr::get($vals, 'issue_id', '');
		$uid        = Arr::get($vals, 'uid', '');
		$message    = Arr::get($vals, 'message', '');

		if ( ! $issue_id || ! $uid || ! $message) return false;

		$sql = 'INSERT INTO '.A11YC_TABLE_ISSUESBBS;
		$sql.= '(`issue_id`,';
		$sql.= '`uid`,';
		$sql.= '`message`,';
		$sql.= '`created_at`) VALUES ';
		$sql.= '(?, ?, ?, ?);';

		return Db::execute(
			$sql,
			array(
				$issue_id,
				$uid,
				$message,
				Arr::get($vals, 'created_at', date('Y-m-d H:i:s'))
			)
		);
	}

	/**
	 * update issuebbs field
	 *
	 * @param  Integer $id
	 * @param  String $field
	 * @param  Mixed  $value
	 * @return Bool
	 */
	public static function updateField($id, $field, $value)
	{
		$id = intval($id);
		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUESBBS.' WHERE `id` = ?;';
		if( ! Db::fetch($sql, array($id))) return false;

		$sql = 'UPDATE '.A11YC_TABLE_ISSUESBBS.' SET `'.$field.'` = ?';
		$sql.= ' WHERE `id` = ?';
		return Db::execute($sql, array($value, $id));
	}
}
