<?php
/**
 * A11yc\Controller\Issue
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Issue
{
	use IssueIndex;
	use IssueRead;
	use IssueUpdate;

	/**
	 * Index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::failures();
	}
	/**
	 * Yet
	 *
	 * @return Void
	 */
	public static function actionYet()
	{
		static::any(0);
	}

	/**
	 * Progress
	 *
	 * @return Void
	 */
	public static function actionProgress()
	{
		static::any(1);
	}

	/**
	 * Done
	 *
	 * @return Void
	 */
	public static function actionDone()
	{
		static::any(2);
	}

	/**
	 * Trash
	 *
	 * @return Void
	 */
	public static function actionTrash()
	{
		static::any(3);
	}

	/**
	 * add Issue
	 *
	 * @return Void
	 */
	public static function actionAdd()
	{
		$is_add = true;
		static::edit($is_add);
	}

	/**
	 * edit Issue
	 *
	 * @return Void
	 */
	public static function actionEdit()
	{
		// use IssueUpdate
		static::edit();
	}

	/**
	 * delete Issue
	 *
	 * @return Void
	 */
	public static function actionDelete()
	{
		// use IssueUpdate
		self::trashControl('delete');
	}

	/**
	 * undelete Issue
	 *
	 * @return Void
	 */
	public static function actionUndelete()
	{
		// use IssueUpdate
		self::trashControl('undelete');
	}

	/**
	 * purge Issue
	 *
	 * @return Void
	 */
	public static function actionPurge()
	{
		// use IssueUpdate
		self::trashControl('purge');
	}

	/**
	 * read Issue
	 *
	 * @return Void
	 */
	public static function actionRead()
	{
		// use IssueRead
		static::issue();
	}
}
