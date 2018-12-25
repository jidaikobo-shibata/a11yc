<?php
/**
 * A11yc\Controller\Icls
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Icls
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * Show Techs Index
	 *
	 * @return Void
	 */
	public static function index()
	{
		View::assign('icls', Model\Icls::fetchAll(), FALSE);
		View::assign('iclssit', Model\Iclssit::fetchAll());
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('title', A11YC_LANG_ICLS_TITLE);
		View::assign('submenu', View::fetchTpl('icls/inc_submenu.php'), FALSE);
		View::assign('body', View::fetchTpl('icls/index.php'), FALSE);
	}

	/**
	 * Action Import
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		$is_waic_imported = Model\Settings::fetch('is_waic_imported');
		if ($is_waic_imported) Util::redirect(A11YC_URL);

		$icls = include(A11YC_PATH.'/resources/icls_default_waic.php');

		foreach ($icls as $criterion => $icl)
		{
			$iclssit_id = 0;
			foreach ($icl as $row)
			{
				$vals = array(
					'criterion' => $criterion,
				);

				// iclssit - situation
				if ( ! is_array($row))
				{
					$vals['value'] = $row;
					$iclssit_id = Model\Iclssit::insert($vals);
				}
				// icls
				else
				{
					$vals = array_merge($row, $vals);
					$vals['situation'] = $iclssit_id;
					Model\Icls::insert($vals);
				}
			}
		}

		Model\Settings::updateField('is_waic_imported', true);
		Util::redirect(A11YC_ICLS_URL.'index');
	}
}
