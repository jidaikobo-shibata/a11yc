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
	 * action form
	 *
	 * @return Void
	 */
	public static function actionForm()
	{
		self::form();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetchTpl('setting/form.php'), false);
	}

	/**
	 * action ua
	 *
	 * @return Void
	 */
	public static function actionUa()
	{
		self::ua();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetchTpl('setting/ua.php'), false);
	}

	/**
	 * action versions
	 *
	 * @return Void
	 */
	public static function actionVersions()
	{
		self::versions();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetchTpl('setting/versions.php'), false);
	}

	/**
	 * action sites
	 *
	 * @return Void
	 */
	public static function actionSites()
	{
		self::sites();
		View::assign('form', View::fetch('form'), false);
		View::assign('body', View::fetchTpl('setting/sites.php'), false);
	}

	/**
	 * settings form
	 *
	 * @return Void
	 */
	public static function form()
	{
		self::dbio();

		// assign
		$force = true;
		View::assign('title',         A11YC_LANG_SETTING_TITLE_BASE);
		View::assign('sample_policy', str_replace("\\n", "\n", A11YC_LANG_SAMPLE_POLICY));
		View::assign('settings',      Model\Setting::fetchAll($force));
		View::assign('standards',     Yaml::each('standards'));
		View::assign('yml',           Yaml::fetch());
		View::assign('submenu',       View::fetchTpl('setting/inc_submenu.php'), FALSE);
		View::assign('form',          View::fetchTpl('setting/inc_form.php'), FALSE);
	}

	/**
	 * dbio
	 *
	 * @return Void
	 */
	private static function dbio()
	{
		if (Input::isPostExists())
		{
			$intvals = array(
				'target_level',
				'selected_method',
				'stop_guzzle',
				'standard',
				'show_results',
				'show_url_results',
				'cache_time',
			);
			$cols = array();
			foreach ($intvals as $v)
			{
				$cols[$v] = intval(Input::post($v, 0));
			}

			// stripslashes
			$stripslashes =array(
				'client_name',
				'declare_date',
				'test_period',
				'dependencies',
				'policy',
				'report',
				'contact',
				'base_url',
				'basic_user',
				'basic_pass',
			);
			foreach ($stripslashes as $v)
			{
				$cols[$v] = stripslashes(Input::post($v, ''));
			}
			$cols['base_url'] = rtrim($cols['base_url'], '/');

			// json_encode values
			foreach (Model\Setting::$json_encodes as $json_encode)
			{
				$values = array();
				if (Input::postArr($json_encode))
				{
					foreach (array_keys(Input::postArr($json_encode)) as $code)
					{
						$values[] = $code;
					}
				}
				$cols[$json_encode] = $values;
			}

			// database io
			$r = true;
			foreach ($cols as $key => $value)
			{
				$r = Model\Setting::update($key, $value);
				if ($r === false) continue;
			}

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

	/**
	 * settings ua
	 *
	 * @return Void
	 */
	public static function ua()
	{
		Model\Ua::dbio();

		// assign
		$force = true;
		View::assign('title',   A11YC_LANG_SETTING_TITLE_UA);
		View::assign('uas',     Model\Ua::fetch($force));
		View::assign('submenu', View::fetchTpl('setting/inc_submenu.php'), FALSE);
		View::assign('form',    View::fetchTpl('setting/inc_ua.php'), FALSE);
	}

	/**
	 * settings versions
	 *
	 * @return Void
	 */
	public static function versions()
	{
		if (Input::isPostExists())
		{
			if (Input::post('protect_data'))
			{
				if (Model\Version::protect())
				{
					Session::add('messages', 'messages', A11YC_LANG_RESULT_PROTECT_DATA_SAVED);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_RESULT_PROTECT_DATA_FAILD);
				}
			}
			else
			{
				if (Model\Version::update())
				{
					Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
				}
				else
				{
					Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
				}
			}
		}

		// assign
		$force = true;
		View::assign('title',        A11YC_LANG_SETTING_TITLE_VERSIONS);
		View::assign('versions',     Model\Version::fetchAll($force));
		View::assign('submenu',      View::fetchTpl('setting/inc_submenu.php'), FALSE);
		View::assign('protect_form', View::fetchTpl('setting/inc_protect.php'), FALSE);
		View::assign('form',         View::fetchTpl('setting/inc_versions.php'), FALSE);
	}

	/**
	 * settings sites
	 *
	 * @return Void
	 */
	public static function sites()
	{
		$sites = Model\Data::fetchSites();
		if (Input::isPostExists())
		{
			self::addNewSite($sites);
			self::changeSite();
			self::changeSiteUrl($sites);
		}
		$sites = Model\Data::fetchSites(true);

		// assign
		View::assign('title',    A11YC_LANG_SETTING_TITLE_SITE);
		View::assign('group_id', Model\Data::groupId(true));
		View::assign('sites',    $sites);
		View::assign('submenu',  View::fetchTpl('setting/inc_submenu.php'), FALSE);
		View::assign('form',     View::fetchTpl('setting/inc_sites.php'), FALSE);
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
			}
			else
			{
				$sites[] = Util::urldec($new_site);
				Model\Data::update('sites', 'global', $sites);
				Session::add('messages', 'message', A11YC_LANG_CTRL_ADDED_NORMALLY);
			}
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
			$site = intval($site);
			if ($site != Model\Data::groupId())
			{
				Model\Data::update('group_id', 'global', $site, 0, 1);
				Session::add('messages', 'message', A11YC_LANG_CTRL_ADDED_NORMALLY);
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
		if (Model\Data::updateUrl($sites[$group_id], $new_url, 0, $group_id))
		{
			Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
		}

		$sites[$group_id] = $new_url;
		Model\Data::update('sites', 'global', $sites, 0, 1);
	}
}
