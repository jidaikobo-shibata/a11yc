<?php
/**
 * A11yc\Controller\Setting
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Setting
{
	/**
	 * action base
	 *
	 * @return Void
	 */
	public static function actionBase()
	{
		self::base();
		View::assign('body', View::fetchTpl('setting/base.php'), false);
	}

	/**
	 * action ua
	 *
	 * @return Void
	 */
	public static function actionUa()
	{
		self::ua();
		View::assign('body', View::fetchTpl('setting/ua.php'), false);
	}

	/**
	 * action version
	 *
	 * @return Void
	 */
	public static function actionVersion()
	{
		self::version();
		View::assign('body', View::fetchTpl('setting/version.php'), false);
	}

	/**
	 * action change version
	 *
	 * @return Void
	 */
	public static function actionChange()
	{
		self::changeVersion();
	}

	/**
	 * action site
	 *
	 * @return Void
	 */
	public static function actionSite()
	{
		self::site();
		View::assign('body', View::fetchTpl('setting/site.php'), false);
	}

	/**
	 * base
	 *
	 * @return Void
	 */
	public static function base()
	{
		if (Input::isPostExists())
		{
			Util::setMassage(Model\Setting::updateAll(Model\Data::postFilter(Model\Setting::$fields)));
		}

		// assign
		View::assign('title',    A11YC_LANG_SETTING_TITLE_BASE);
		View::assign('settings', Model\Setting::fetchAll(true));
		View::assign('form',     View::fetchTpl('setting/inc_base.php'), FALSE);
	}

	/**
	 * settings ua
	 *
	 * @return Void
	 */
	public static function ua()
	{
		if (Input::isPostExists())
		{
			self::updateUa();
		}

		View::assign('title', A11YC_LANG_SETTING_TITLE_UA);
		View::assign('uas',   Model\Ua::fetch(true));
		View::assign('form',  View::fetchTpl('setting/inc_ua.php'), FALSE);
	}

	/**
	 * settings updateUa
	 *
	 * @return Void
	 */
	private static function updateUa()
	{
		$names   = Input::postArr('name');
		$strs    = Input::postArr('str');
		$deletes = Input::postArr('delete');

		// id 1 is default ua. can not delete or edit str.
		$strs[1] = '';
		if (isset($deletes[1])) unset($deletes[1]);

		$value = array();
		foreach (array_keys($names) as $id)
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

		Util::setMassage(Model\Setting::update('user_agents', $value));
	}

	/**
	 * settings version
	 *
	 * @return Void
	 */
	public static function version()
	{
		if (Input::isPostExists())
		{
			if (Input::post('protect_data'))
			{
				Util::setMassage(
					Model\Version::protect(),
					A11YC_LANG_RESULT_PROTECT_DATA_SAVED,
					A11YC_LANG_RESULT_PROTECT_DATA_FAILD
				);
			}
			else
			{
				Util::setMassage(self::updateVersion());
			}
		}

		// assign
		$force = true;
		View::assign('title',        A11YC_LANG_SETTING_TITLE_VERSION);
		View::assign('versions',     Model\Version::fetchAll($force));
		View::assign('protect_form', View::fetchTpl('setting/inc_version_protect.php'), FALSE);
		View::assign('use_form',     View::fetchTpl('setting/inc_version_use.php'), FALSE);
		View::assign('form',         View::fetchTpl('setting/inc_version.php'), FALSE);
	}


	/**
	 * chage version
	 *
	 * @return Void
	 */
	public static function changeVersion()
	{
		Util::setMassage(Model\Version::changeVersion(Input::post('target_version', 0)));
		Util::redirect(A11YC_SETTING_URL.'version');
	}

	/**
	 * update
	 *
	 * @return Void
	 */
	private static function updateVersion()
	{
		$names   = Input::postArr('name');
		$trashes = Input::postArr('trash');
		$deletes = Input::postArr('delete');

		// update
		$vals = array();
		foreach ($names as $version => $name)
		{
			$vals[$version] = array(
				'name'  => $name,
				'trash' => isset($trashes[$version]) ? 0 : 1,
			);
		}
		$r = Model\Data::update('version', 'common', $vals);

		// delete
		foreach ($deletes as $version)
		{
			Model\Version::delete($version);
			Session::add(
				'messages',
				'messages',
				sprintf(A11YC_LANG_CTRL_DELETE_DONE, $names[$version])
			);
		}

		return $r;
	}

	/**
	 * settings site
	 *
	 * @return Void
	 */
	public static function site()
	{
		$sites = Model\Data::fetchSites();
		if (Input::isPostExists())
		{
			self::addNewSite($sites); // redirect
			self::changeSite(); // redirect
			self::changeSiteUrl($sites);
		}
		$sites = Model\Data::fetchSites(true);

		// assign
		View::assign('title',    A11YC_LANG_SETTING_TITLE_SITE);
		View::assign('group_id', Model\Data::groupId(true));
		View::assign('sites',    $sites);
		View::assign('form',     View::fetchTpl('setting/inc_site.php'), FALSE);
	}

	/**
	 * add new site
	 *
	 * @param Array $sites
	 * @return Void
	 */
	private static function addNewSite($sites)
	{
		if ($new_site = Input::post('new_site', false))
		{
			if (in_array($new_site, $sites))
			{
				Session::add('messages', 'errors', A11YC_LANG_CTRL_ALREADY_EXISTS);
				return;
			}

			$is_add = empty($sites);
			$site_id = count($sites) + 1;
			$sites[$site_id] = Util::urldec($new_site);
			if ($is_add)
			{
				Model\Data::insert('sites', 'global', $sites);
			}
			else
			{
				Model\Data::update('sites', 'global', $sites);
			}
			setcookie('a11yc_group_id', $site_id, time() + 60 * 60 * 24 * 60);
			Model\Data::setGroupId($site_id);
			Model\Setting::update('target_level', 2);
			Session::remove('messages', 'errors');
			Session::add('messages', 'messages', A11YC_LANG_CTRL_ADDED_NORMALLY);
			Util::redirect(A11YC_SETTING_URL.'site');
		}
	}

	/**
	 * change target site
	 *
	 * @return Void
	 */
	private static function changeSite()
	{
		if ($site = Input::post('site'))
		{
			if ($site != Model\Data::groupId())
			{
				Session::remove('messages', 'errors');
				setcookie('a11yc_group_id', '', time() - 60 * 60 * 24);
				setcookie('a11yc_group_id', $site, time() + 60 * 60 * 24 * 60);
				Session::add('messages', 'messages', A11YC_LANG_CTRL_ADDED_NORMALLY);
				Util::redirect(A11YC_SETTING_URL.'site');
			}
		}
	}

	/**
	 * change target site url
	 *
	 * @param Array $sites
	 * @return Void
	 */
	private static function changeSiteUrl($sites)
	{
		if (Input::post('change_url_confirm') == false) return;

		$group_id = Input::post('change_url_target');
		if ( ! isset($sites[$group_id])) return;

		$new_url = Input::post('change_url_new_url', '');
		if (empty($new_url)) return;

		if (in_array($new_url, $sites))
		{
			Session::add('messages', 'errors', A11YC_LANG_CTRL_ALREADY_EXISTS);
			return;
		}

		// update site
		Util::setMassage(Model\Data::updateUrl($sites[$group_id], $new_url, 0, $group_id));

		$sites[$group_id] = $new_url;
		Model\Data::update('sites', 'global', $sites, 0, 1);
	}
}
