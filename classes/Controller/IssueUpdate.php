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

trait IssueUpdate
{
	/**
	 * add/edit Issue
	 *
	 * @param Bool $is_add
	 * @param Array $users
	 * @param INTEGER $current_user_id
	 * @return Void
	 */
	public static function edit($is_add = false, $users = array(), $current_user_id = NULL)
	{
		$item = array();
		if ($id = intval(Input::get('id')))
		{
			if ($id === 0) Util::error('id not found');
			$item = Model\Issue::fetch($id);
			if (Arr::get($item, 'trash') == 1) Util::error('issue not found');
		}

		// set current user
		$current_user_id = self::setCurrentUser($current_user_id, $users);

		// create or update
		if (Input::isPostExists())
		{
			if ($is_add)
			{
				$item = self::add();
				$newfilename = File::uploadImg('issues', '');
			}
			else
			{
				$item = self::update($id);
				$newfilename = File::uploadImg('issues', '', $item['image_path']);
			}
			Model\Issue::updatePartial($item['id'], 'image_path', $newfilename);
			Util::redirect(A11YC_ISSUE_URL.'edit&amp;id='.$item['id']);
		}

		$item = $is_add ? self::assignAdd($item) : $item;
		View::assign('uid',   Arr::get($item, 'uid', $current_user_id));
		View::assign('item',  $item);
		View::assign('title', $is_add ? A11YC_LANG_ISSUE_ADD : A11YC_LANG_ISSUE_EDIT);
		View::assign('form',  View::fetchTpl('issue/form.php'), FALSE);
		View::assign('body',  View::fetchTpl('issue/edit.php'), FALSE);
	}

	/**
	 * set Current User
	 *
	 * @param Integer|Null $current_user_id
	 * @param Array $users
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
	 * @param Array $item
	 * @return Array
	 */
	private static function assignAdd($item)
	{
		$errs = Yaml::each('errors');
		$err_id = Input::get('err_id', '');

		$err_techs = Arr::get($errs, "{$err_id}.techs", array());
		$techs = array();
		foreach ($err_techs as $err_tech)
		{
			$techs[] = $err_tech;
		}

		$item['url'] = Util::urldec(Input::get('url', 'common'));
		$item['criterion'] = Input::get('criterion', '');
		$item['techs'] = $techs;
		$item['error_message'] = Arr::get($errs, "{$err_id}.message");
		$item['html'] = Input::get('src', '');
		$item['n_or_e'] = intval(Arr::get($errs, "{$err_id}.n_or_e", 1));
		return $item;
	}

	/**
	 * add Issue
	 *
	 * @return Array
	 */
	private static function args()
	{
		$args = array();
		// foreach (Model\Issue::$fields as $key => $default)
		// {
		// 	$args[$key] = Input::post($key, $default);
		// }
$args = Model\Data::postfilter(Model\Issue::$fields);

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
var_dump($args);
echo '</textarea>';
die();

		$args['output'] = Input::post('output', false); // checkbox
		$args['techs'] = Input::postArr('techs', array()); // checkbox
		return $args;
	}

	/**
	 * url
	 *
	 * @param Bool $is_common
	 * @return String
	 */
	private static function url($is_common = false)
	{
		$url = Input::post('url', Model\Data::baseUrl());
		return $is_common === true ? 'common' : $url;
	}

	/**
	 * add Issue
	 *
	 * @return Array
	 */
	private static function add()
	{
		$args = self::args();
		Util::setMassage($id = Model\Issue::insert(self::url($args['is_common']), $args));
		return $id ? Model\Issue::fetch($id, true) : array();
	}

	/**
	 * update Issue
	 *
	 * @param integer $id
	 * @return Array
	 */
	private static function update($id)
	{
		Util::setMassage(Model\Issue::update(self::url(), $id, self::args()));
		return Model\Issue::fetch($id, true);
	}

	/**
	 * trashControl
	 *
	 * @param String $act [delete, undelete, purge]
	 * @return Void
	 */
	private static function trashControl($act)
	{
		$id = intval(Input::get('id'));
		$item = Model\Issue::fetch($id);
		if (empty($item)) Util::error('issue not found');

		$r = false;
		if (
			$act == 'delete' && $item['trash'] == 0 ||
			$act == 'undelete' && $item['trash'] == 1
		)
		{
			$r = Model\Issue::updatePartial($id, 'trash', ! $item['trash']);
		}
		elseif ($act == 'purge' && $item['trash'] == 1)
		{
			$r = Model\Issue::purge($id);
		}

		$act = strtoupper($act);
		$mess_type = $r ? 'messages' : 'errors';
		$mess_str  = $r ?
							 sprintf(constant('A11YC_LANG_CTRL_'.$act.'_DONE'), 'id: '.$id) :
							 sprintf(constant('A11YC_LANG_CTRL_'.$act.'_FAILED'), 'id: '.$id);
		Session::add('messages', $mess_type, $mess_str);
		Util::redirect(A11YC_ISSUE_URL.'yet');
	}

}
