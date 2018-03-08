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
	 * add Issue
	 *
	 * @return Void
	 */
	public static function actionAdd()
	{
		static::edit($is_add = true);
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
	 * view Issue
	 *
	 * @return Void
	 */
	public static function actionView()
	{
		static::view();
	}

	/**
	 * add/edit Issue
	 *
	 * @param  Bool $is_add
	 * @return Void
	 */
	public static function edit($is_add = false)
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

		$user = Users::fetchCurrentUser();

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
				$issue = Model\Issues::fetch($id, $force = 1);
			}
		}

		$current_user = Users::fetchCurrentUser();
		View::assign('users',         Users::fetchUsersOpt());
		View::assign('is_new',        $is_add);
		View::assign('issue_id',      Arr::get($issue, 'id', ''));
		View::assign('is_common',     Arr::get($issue, 'is_common', ''));
		View::assign('url',           Arr::get($issue, 'url', $url));
		View::assign('criterion',     Arr::get($issue, 'criterion', $criterion));
		View::assign('html',          Arr::get($issue, 'html', ''));
		View::assign('n_or_e',        intval(Arr::get($issue, 'n_or_e', 0)));
		View::assign('statuses',      Values::issueStatus());
		View::assign('status',        intval(Arr::get($issue, 'status', 0)));
		View::assign('tech_url',      Arr::get($issue, 'tech_url', ''));
		View::assign('error_message', Arr::get($issue, 'error_message', ''));
		View::assign('uid',           Arr::get($issue, 'uid', $current_user['id']));
		View::assign('title', $is_add ? A11YC_LANG_ISSUES_ADD : A11YC_LANG_ISSUES_EDIT);
		View::assign('body',  View::fetchTpl('issues/form.php'), FALSE);
	}

	/**
	 * view Issue
	 *
	 * @return Void
	 */
	public static function view()
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issues::fetch($id);
		if ( ! $issue) Util::error('issue not found');
		$current_user = Users::fetchCurrentUser();

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
						'uid'      => $current_user['id'],
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

		View::assign('current_user', $current_user);
		View::assign('status',       Values::issueStatus());
		View::assign('users',        Users::fetchUsersOpt());
		View::assign('issue',        $issue);
		View::assign('bbss',         Model\Issuesbbs::fetchAll($issue['id']));
		View::assign('title',        A11YC_LANG_ISSUES_TITLE);
		View::assign('body',         View::fetchTpl('issues/view.php'), FALSE);
	}
}
