<?php
/**
 * A11yc\Controller\Settings
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Settings
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
		View::assign('body', View::fetchTpl('settings/form.php'), false);
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
		View::assign('body', View::fetchTpl('settings/ua.php'), false);
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
		View::assign('body', View::fetchTpl('settings/versions.php'), false);
	}

	/**
	 * settings form
	 *
	 * @return Void
	 */
	public static function form()
	{
		Model\Settings::dbio();

		// assign
		View::assign('title',         A11YC_LANG_SETTINGS_TITLE_BASE);
		View::assign('sample_policy', str_replace("\\n", "\n", A11YC_LANG_SAMPLE_POLICY));
		View::assign('settings',      Model\Settings::fetchAll($force = 1));
		View::assign('standards',     Yaml::each('standards'));
		View::assign('yml',           Yaml::fetch());
		View::assign('submenu',       View::fetchTpl('settings/inc_submenu.php'), FALSE);
		View::assign('form',          View::fetchTpl('settings/inc_form.php'), FALSE);
	}

	/**
	 * settings ua
	 *
	 * @return Void
	 */
	public static function ua()
	{
		Model\Uas::dbio();

		// assign
		View::assign('title',   A11YC_LANG_SETTINGS_TITLE_UA);
		View::assign('uas',     Model\Uas::fetch($force = true));
		View::assign('submenu', View::fetchTpl('settings/inc_submenu.php'), FALSE);
		View::assign('form',    View::fetchTpl('settings/inc_ua.php'), FALSE);
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
				Model\Versions::protect();
			}
			else
			{
				Model\Versions::update();
			}
		}

		// assign
		View::assign('title',        A11YC_LANG_SETTINGS_TITLE_VERSIONS);
		View::assign('versions',     Model\Versions::fetch($force = true));
		View::assign('submenu',      View::fetchTpl('settings/inc_submenu.php'), FALSE);
		View::assign('protect_form', View::fetchTpl('settings/inc_protect.php'), FALSE);
		View::assign('form',         View::fetchTpl('settings/inc_versions.php'), FALSE);
	}
}