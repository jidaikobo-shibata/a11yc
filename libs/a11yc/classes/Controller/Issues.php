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
		static::index();
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
	 * view Issue
	 *
	 * @return Void
	 */
	public static function actionView()
	{
		static::view();
	}

	/**
	 * Issue index
	 *
	 * @return Void
	 */
	public static function index()
	{
		$issues = array(
			'yet' =>      Model\Issues::fetchByStatus(0),
			'progress' => Model\Issues::fetchByStatus(1),
			'done' =>     Model\Issues::fetchByStatus(2),
			'trash' =>    Model\Issues::fetchTrashed(),
		);
		View::assign('yml',      Yaml::each('techs'));
		View::assign('issues',   $issues);
		View::assign('failures', Model\Checklist::fetchFailures());
		View::assign('title',    A11YC_LANG_ISSUES_TITLE.A11YC_LANG_ISSUES_STATUS);
		View::assign('body',     View::fetchTpl('issues/index.php'), FALSE);
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
			$issue     = Model\Issues::fetch($id);
			$url       = Arr::get($issue, 'url');
			$criterion = Arr::get($issue, 'criterion');
		}
		if ( ! $url || ! $criterion) Util::error();

		if (is_null($current_user_id))
		{
			$current_user = Users::fetchCurrentUser();
			$current_user_id = Arr::get($current_user, 'id', 1);
			$users = Users::fetchUsersOpt();
		}

		if (Input::isPostExists())
		{
			$args = array(
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
			extract($args);

			// add
			if ($is_add)
			{
				if ($issue_id = Model\Issues::add($args))
				{
					Session::add('messages', 'messages', A11YC_LANG_ISSUES_ADDED);
					Util::redirect(A11YC_ISSUES_EDIT_URL.$issue_id);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_ISSUES_ADDED_FAILED);
				}
			}

			// delete
			elseif (Input::post('is_delete'))
			{
				$issue = Model\Issues::fetch($id);
				$r = Model\Issues::delete($id);
				$mess_type = $r ? 'messages' : 'errors';
				$mess_str  = $r ?
									 sprintf(A11YC_LANG_PAGES_PURGE_DONE, A11YC_LANG_ISSUES_TITLE) :
									 sprintf(A11YC_LANG_PAGES_PURGE_DONE, A11YC_LANG_PAGES_PURGE_FAILED);
				Session::add('messages', $mess_type, $mess_str);
				Util::redirect(A11YC_CHECKLIST_URL.Util::urlenc($url));
			}

			// update
			else
			{
				$r = true;
				$r = Model\Issues::updateField($id, 'is_common',     Input::post('is_common', 0));
				$r = Model\Issues::updateField($id, 'html',          Input::post('html', ''));
				$r = Model\Issues::updateField($id, 'n_or_e',        Input::post('n_or_e', 0));
				$r = Model\Issues::updateField($id, 'status',        Input::post('status', 0));
				$r = Model\Issues::updateField($id, 'tech_url',      Input::post('tech_url', ''));
				$r = Model\Issues::updateField($id, 'error_message', Input::post('error_message', ''));
				$r = Model\Issues::updateField($id, 'uid',           Input::post('uid', 1));

				if ($r)
				{
					Session::add('messages', 'messages', A11YC_LANG_ISSUES_EDITED);
					Util::redirect(A11YC_ISSUES_EDIT_URL.$id);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_ISSUES_EDITED_FAILED);
				}
				$force = true;
				$issue = Model\Issues::fetch($id, $force);
			}
		}

		View::assign('users',         $users);
		View::assign('is_new',        $is_add);
		View::assign('issue_id',      Arr::get($issue, 'id', ''));
		View::assign('is_common',     Arr::get($issue, 'is_common', ''));
		View::assign('url',           Arr::get($issue, 'url', $url));
		View::assign('criterion',     Arr::get($issue, 'criterion', $criterion));
		View::assign('statuses',      Values::issueStatus());
		View::assign('status',        intval(Arr::get($issue, 'status', 0)));

		if ($is_add)
		{
			$errs = Yaml::each('errors');
			$err_id    = Input::get('err_id', '');

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
		else
		{
			View::assign('tech_url',      Arr::get($issue, 'tech_url', ''));
			View::assign('error_message', Arr::get($issue, 'error_message', ''));
			View::assign('html',          Arr::get($issue, 'html', ''));
			View::assign('n_or_e',        intval(Arr::get($issue, 'n_or_e', 0)));
		}

		View::assign('uid',           Arr::get($issue, 'uid', $current_user_id));
		View::assign('title', $is_add ? A11YC_LANG_ISSUES_ADD : A11YC_LANG_ISSUES_EDIT);
		View::assign('form',          View::fetchTpl('issues/form.php'), FALSE);
		View::assign('body',          View::fetchTpl('issues/edit.php'), FALSE);
	}

	/**
	 * view Issue
	 *
	 * @param  Array $users
	 * @param  INTEGER $current_user_id
	 * @param  BOOL $current_user_id
	 * @return Void
	 */
	public static function view($users = array(), $current_user_id = NULL, $is_admin = false)
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issues::fetch($id);
		if ( ! $issue) Util::error('issue not found');

		if (is_null($current_user_id))
		{
			$current_user = Users::fetchCurrentUser();
			$current_user_id = $current_user['id'];
			$users = Users::fetchUsersOpt();
			$is_admin = $current_user[0] == 'root';
		}

		if (Input::isPostExists())
		{
			$r = true;

			// update status
			if ($issue['status'] != Input::post('status'))
			{
				$r = Model\Issues::updateField($id, 'status', Input::post('status', 0));
			}

			// update message
			foreach (Input::postArr('a11yc_issuesbbs') as $k => $v)
			{
				if ($k == 'new' && $v)
				{
					$args = array(
						'issue_id' => $id,
						'uid'      => $current_user_id,
						'message'  => $v,
					);
					$r = Model\Issuesbbs::add($args);
				}
				else if($k != 'new')
				{
					$r = Model\Issuesbbs::updateField($k, 'message', $v);
				}
			}

			$mess_type = $r ? 'messages' : 'errors';
			$mess_str  = $r ? A11YC_LANG_ISSUES_EDITED : A11YC_LANG_ISSUES_EDITED_FAILED;
			Session::add('messages', $mess_type, $mess_str);
		}

		View::assign('current_user_id', $current_user_id);
		View::assign('status',          Values::issueStatus());
		View::assign('is_admin',        $is_admin);
		View::assign('users',           $users);
		View::assign('issue',           $issue);
		View::assign('bbss',            Model\Issuesbbs::fetchAll($issue['id']));
		View::assign('title',           A11YC_LANG_ISSUES_TITLE);
		View::assign('form',            View::fetchTpl('issues/message.php'), FALSE);
		View::assign('body',            View::fetchTpl('issues/view.php'), FALSE);
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
		if ( ! $issue) Util::error('issue not found');

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
		Util::redirect(A11YC_ISSUES_INDEX_URL);
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
		if ( ! $issue) Util::error('issue not found');

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
		Util::redirect(A11YC_ISSUES_INDEX_URL);
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
		Util::redirect(A11YC_ISSUES_INDEX_URL);
	}
}
