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

trait IssueRead
{
	/**
	 * view Issue
	 *
	 * @param Array $users
	 * @param INTEGER $current_user_id
	 * @param BOOL $current_user_id
	 * @return Void
	 */
	public static function issue($users = array(), $current_user_id = NULL, $is_admin = false)
	{
		$id = intval(Input::get('id'));
		$issue = Model\Issue::fetch($id);
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
			// update status
			if ($issue['status'] != Input::post('status'))
			{
				Model\Issue::updatePartial($id, 'status', Input::post('status', 0));
			}

			// update message
			$bbs = array();
			foreach (Input::postArr('a11yc_issuesbbs') as $k => $v)
			{
				if (empty($v)) continue;
				$vals = array(
					'uid'        => $current_user_id,
					'message'    => $v,
				);

				$date_key = is_numeric($k) ? 'updated_at' : 'created_at' ;
				$vals[$date_key] = date('Y-m-d H:i:s');

				$bbs[] = $vals;
			}
			$r = Model\Issue::updatePartial($id, 'bbs', $bbs);

			$mess_type = $r ? 'messages' : 'errors';
			$mess_str  = $r ? A11YC_LANG_UPDATE_SUCCEED : A11YC_LANG_UPDATE_FAILED;
			Session::add('messages', $mess_type, $mess_str);
			$issue = Model\Issue::fetch($id, true);
		}

		View::assign('current_user_id', $current_user_id);
		View::assign('status',          Values::issueStatus());
		View::assign('is_admin',        $is_admin);
		View::assign('users',           $users);
		View::assign('issue',           $issue);
		View::assign('title',           A11YC_LANG_ISSUE_TITLE);
		View::assign('form',            View::fetchTpl('issue/message.php'), FALSE);
		View::assign('body',            View::fetchTpl('issue/view.php'), FALSE);
	}
}
