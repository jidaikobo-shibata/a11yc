<?php
/**
 * A11yc\Controller\Issues
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Issues
{
	/**
	 * Index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		Issues\Index::failures();
	}
	/**
	 * Yet
	 *
	 * @return Void
	 */
	public static function actionYet()
	{
		Issues\Index::any(0);
	}

	/**
	 * Progress
	 *
	 * @return Void
	 */
	public static function actionProgress()
	{
		Issues\Index::any(1);
	}

	/**
	 * Done
	 *
	 * @return Void
	 */
	public static function actionDone()
	{
		Issues\Index::any(2);
	}

	/**
	 * Trash
	 *
	 * @return Void
	 */
	public static function actionTrash()
	{
		Issues\Index::any(3);
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
		static::edit();
	}

	/**
	 * delete Issue
	 *
	 * @return Void
	 */
	public static function actionDelete()
	{
		static::delete();
	}

	/**
	 * undelete Issue
	 *
	 * @return Void
	 */
	public static function actionUndelete()
	{
		static::undelete();
	}

	/**
	 * purge Issue
	 *
	 * @return Void
	 */
	public static function actionPurge()
	{
		static::purge();
	}

	/**
	 * read Issue
	 *
	 * @return Void
	 */
	public static function actionRead()
	{
		Issues\Read::issue();
	}

	/**
	 * add/edit Issue
	 *
	 * @param  Bool $is_add
	 * @param  Array $users
	 * @param  INTEGER $current_user_id
	 * @return Void
	 */
	public static function edit($is_add = false, $users = array(), $current_user_id = NULL)
	{
		$issue = array();
		if ($is_add)
		{
			$url       = Util::urldec(Input::get('url', '', FILTER_VALIDATE_URL));
			$criterion = Input::get('criterion', '');
		}
		else
		{
			$id        = intval(Input::get('id'));
			if ($id === 0) Util::error('id not found');
			$issue     = Model\Issues::fetch($id);
			if (Arr::get($issue, 'trash') == 1) Util::error('issue not found');
			$url       = Arr::get($issue, 'url');
			$criterion = Arr::get($issue, 'criterion');
		}

		// set current user
		$current_user_id = self::setCurrentUser($current_user_id, $users);

		// create or update
		if (Input::isPostExists())
		{
			// add
			if ($is_add)
			{
				$id = self::add($url, $criterion);
				$newfilename = Upload::img('issues', $id);
			}

			// update
			else if (isset($id) && is_numeric($id))
			{
				$issue = self::update($id);
				$newfilename = Upload::img('issues', $id, $issue['image_path']);
			}
			Model\Issues::updateField($id, 'image_path', $newfilename);

			Util::redirect(A11YC_ISSUES_EDIT_URL.$id);
		}

		View::assign('is_new',    $is_add);
		View::assign('issue_title', Arr::get($issue, 'title', ''));
		View::assign('issue_id',  Arr::get($issue, 'id', ''));
		View::assign('is_common', Arr::get($issue, 'is_common', ''));
		View::assign('url',       Arr::get($issue, 'url', $url));
		View::assign('criterion', Arr::get($issue, 'criterion', $criterion));
		View::assign('image_path', Arr::get($issue, 'image_path', ''));
		View::assign('statuses',  Values::issueStatus());
		View::assign('status',    intval(Arr::get($issue, 'status', 0)));

		if ($is_add)
		{
			self::assignAdd();
		}
		else
		{
			View::assign('tech_url',      Arr::get($issue, 'tech_url', ''));
			View::assign('error_message', Arr::get($issue, 'error_message', ''));
			View::assign('html',          Arr::get($issue, 'html', ''));
			View::assign('n_or_e',        intval(Arr::get($issue, 'n_or_e', 0)));
		}

		View::assign('uid',   Arr::get($issue, 'uid', $current_user_id));
		View::assign('title', $is_add ? A11YC_LANG_ISSUES_ADD : A11YC_LANG_ISSUES_EDIT);
		View::assign('form',  View::fetchTpl('issues/form.php'), FALSE);
		View::assign('body',  View::fetchTpl('issues/edit.php'), FALSE);
	}

	/**
	 * set Current User
	 *
	 * @param  Integer|Null $current_user_id
	 * @param  Array $users
	 * @return Integer
	 */
	private static function setCurrentUser($current_user_id = NULL, $users)
	{
		if (is_null($current_user_id))
		{
			$current_user = Users::fetchCurrentUser();
			$current_user_id = Arr::get($current_user, 'id', 1);
			$users = Users::fetchUsersOpt();
		}
		View::assign('users', $users);
		return $current_user_id;
	}

	/**
	 * assign add ctrl
	 *
	 * @return Void
	 */
	private static function assignAdd()
	{
		$errs = Yaml::each('errors');
		$err_id = Input::get('err_id', '');

		$err_techs = Arr::get($errs, "{$err_id}.techs", array());
		$techs_links = array();
		foreach ($err_techs as $err_tech)
		{
			$techs_links[] = A11YC_REF_WCAG20_TECH_URL.$err_tech.'.html';
		}

		View::assign('tech_url',      join("\n", $techs_links));
		View::assign('error_message', Arr::get($errs, "{$err_id}.message"));
		View::assign('html',          Input::get('src', ''));
		View::assign('n_or_e',        intval(Arr::get($errs, "{$err_id}.n_or_e", 1)));
	}

	/**
	 * add Issue
	 *
	 * @param  string $url
	 * @param  string $criterion
	 * @return Integer|Bool
	 */
	private static function add($url = '', $criterion = '')
	{
		$args = array(
			'title'         => Input::post('title', ''),
			'is_common'     => Input::post('is_common', false),
			'url'           => $url,
			'criterion'     => $criterion,
			'html'          => Input::post('html', ''),
			'n_or_e'        => Input::post('n_or_e', 0),
			'status'        => Input::post('status', 0),
			'tech_url'      => Input::post('tech_url', ''),
			'error_message' => Input::post('error_message', ''),
			'uid'           => Input::post('uid', 1),
		);
//		extract($args);

		if ($issue_id = Model\Issues::insert($args))
		{
			Session::add('messages', 'messages', A11YC_LANG_ISSUES_ADDED);
		}
		else
		{
			Session::add('messages', 'errors', A11YC_LANG_ISSUES_ADDED_FAILED);
		}

		return $issue_id;
	}

	/**
	 * update Issue
	 *
	 * @param  integer $id
	 * @return Array
	 */
	private static function update($id)
	{
		$r0 = Model\Issues::updateField($id, 'title',         Input::post('title', ''));
		$r1 = Model\Issues::updateField($id, 'is_common',     Input::post('is_common', 0));
		$r2 = Model\Issues::updateField($id, 'html',          Input::post('html', ''));
		$r3 = Model\Issues::updateField($id, 'n_or_e',        Input::post('n_or_e', 0));
		$r4 = Model\Issues::updateField($id, 'status',        Input::post('status', 0));
		$r5 = Model\Issues::updateField($id, 'tech_url',      Input::post('tech_url', ''));
		$r6 = Model\Issues::updateField($id, 'error_message', Input::post('error_message', ''));
		$r7 = Model\Issues::updateField($id, 'uid',           Input::post('uid', 1));

		if ($r0 && $r1 && $r2 && $r3 && $r4 && $r5 && $r6 && $r7)
		{
			Session::add('messages', 'messages', A11YC_LANG_ISSUES_EDITED);
		}
		else
		{
			Session::add('messages', 'errors', A11YC_LANG_ISSUES_EDITED_FAILED);
		}
		$force = true;
		$issue = Model\Issues::fetch($id, $force);
		return $issue;
	}

	/**
	 * Issue delete
	 *
	 * @return Void
	 */
	public static function delete()
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issues::fetch($id);
		if (empty($issue)) Util::error('issue not found');

		$r = false;
		if ($issue['trash'] != 1)
		{
			$r = Model\Issues::updateField($id, 'trash', 1);
		}

		$mess_type = $r ? 'messages' : 'errors';
		$mess_str  = $r ?
							 sprintf(A11YC_LANG_PAGES_DELETE_DONE, 'id: '.$id) :
							 sprintf(A11YC_LANG_PAGES_DELETE_FAILED, 'id: '.$id);
		Session::add('messages', $mess_type, $mess_str);
		Util::redirect(A11YC_ISSUES_BASE_URL.'index');
	}

	/**
	 * Issue undelete
	 *
	 * @return Void
	 */
	public static function undelete()
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issues::fetch($id);
		if (empty($issue)) Util::error('issue not found');

		$r = false;
		if ($issue['trash'] != 0)
		{
			$r = Model\Issues::updateField($id, 'trash', 0);
		}

		$mess_type = $r ? 'messages' : 'errors';
		$mess_str  = $r ?
							 sprintf(A11YC_LANG_PAGES_UNDELETE_DONE, 'id: '.$id) :
							 sprintf(A11YC_LANG_PAGES_UNDELETE_FAILED, 'id: '.$id);
		Session::add('messages', $mess_type, $mess_str);
		Util::redirect(A11YC_ISSUES_BASE_URL.'index');
	}

	/**
	 * Issue purge
	 *
	 * @return Void
	 */
	public static function purge()
	{
		$id = intval(Input::get('id'));
		$r = Model\Issues::purge($id);

		$mess_type = $r ? 'messages' : 'errors';
		$mess_str  = $r ?
							 sprintf(A11YC_LANG_PAGES_PURGE_DONE, 'id: '.$id) :
							 sprintf(A11YC_LANG_PAGES_PURGE_FAILED, 'id: '.$id);
		Session::add('messages', $mess_type, $mess_str);
		Util::redirect(A11YC_ISSUES_BASE_URL.'index');
	}
}
