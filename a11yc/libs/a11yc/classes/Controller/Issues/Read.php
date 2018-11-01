<?php
/**
 * A11yc\Controller\Issues\Read
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller\Issues;

use A11yc\Model;

class Read
{
	/**
	 * view Issue
	 *
	 * @param  Array $users
	 * @param  INTEGER $current_user_id
	 * @param  BOOL $current_user_id
	 * @return Void
	 */
	public static function issue($users = array(), $current_user_id = NULL, $is_admin = false)
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issues::fetch($id);
		if (empty($issue) || Arr::get($issue, 'trash') == 1) Util::error('issue not found');

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
}
