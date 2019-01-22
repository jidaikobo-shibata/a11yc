<?php
/**
 * A11yc\Controller\Icl
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Icl
{
	/**
	 * index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::index();
	}

	/**
	 * view
	 *
	 * @return Void
	 */
	public static function actionView()
	{
		static::view();
	}

	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function actionEdit()
	{
		static::edit();
	}

	/**
	 * delete
	 *
	 * @return Void
	 */
	public static function actionDelete()
	{
		self::trashControl('delete');
	}

	/**
	 * undelete
	 *
	 * @return Void
	 */
	public static function actionUndelete()
	{
		self::trashControl('undelete');
	}

	/**
	 * purge
	 *
	 * @return Void
	 */
	public static function actionPurge()
	{
		self::trashControl('purge');
	}

	/**
	 * Action Import
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		$is_waic_imported = Model\Setting::fetch('is_waic_imported');
		if ($is_waic_imported) Util::redirect(A11YC_URL);

		$icls = include(A11YC_PATH.'/resources/icls_default_waic.php');

		foreach ($icls as $criterion => $icl)
		{
			$iclssit_id = 0;
			foreach ($icl as $row)
			{
				$values = array(
					'criterion' => Util::code2key($criterion),
				);

				// iclssit - situation
				if ( ! is_array($row))
				{
					$values['title'] = $row;
					$values['seq'] = 0;
					$values['is_sit'] = true;
					$iclssit_id = Model\Data::insert('iclsit', 'common', $values);
				}
				// icls
				else
				{
					$values = array_merge($row, $values);
					$values['situation'] = $iclssit_id;
					Model\Data::insert('icl', 'common', $values);
				}
			}
		}

		Model\Setting::update('is_waic_imported', true);
		Util::redirect(A11YC_ICL_URL.'index');
	}

	/**
	 * index
	 *
	 * @return Void
	 */
	public static function index()
	{
		// update
		if (Input::isPostExists())
		{
			$icls = Input::postArr('icls');
			if (empty($icls)) return;
			Model\Setting::update('icl', $icls);
		}

		View::assign('is_view', false);
		View::assign('checks',  Model\Setting::fetch('icl', array(), true));
		View::assign('techs',   Yaml::each('techs'));
		View::assign('icls',    Model\Icl::fetch4ImplementChecklist(), FALSE);
		View::assign('title',   A11YC_LANG_ICL_TITLE);
		View::assign('submenu', View::fetchTpl('icl/inc_submenu.php'), FALSE);
		View::assign('body',    View::fetchTpl('icl/implements_checklist.php'), FALSE);
	}

	/**
	 * view
	 *
	 * @return Void
	 */
	public static function view()
	{
		View::assign('is_view', true);
		View::assign('techs',   Yaml::each('techs'));
		View::assign('icls',    Model\Icl::fetch4ImplementChecklist(), FALSE);
		View::assign('title',   A11YC_LANG_ICL_TITLE);
		View::assign('body',    View::fetchTpl('icl/implements_checklist.php'), FALSE);

		View::display(array(
				'inc_report_header.php',
				'body.php',
				'inc_report_footer.php',
			));
		exit();
	}

	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function edit()
	{
		$id = Input::get('id', false);
		$is_sit = Input::get('is_sit', false);
		$qstr = $is_sit ? '&amp;is_sit=1' : '';
		$item = $id ? Model\Icl::fetch($id) : array();

		// create or update
		if (Input::isPostExists())
		{
			$id = Input::post('is_add', false) ? self::add($is_sit) : self::update($id, $is_sit);
			Util::redirect(A11YC_ICL_URL.'edit&amp;id='.$id.$qstr);
		}

		View::assign('item', $item);

		$form = $is_sit ? 'form_sit' :'form';
		View::assign('is_add', ! $id);
		View::assign('qstr', $qstr);
		View::assign('iclsits', Model\Icl::fetchAll('iclsit'));
		View::assign('title', A11YC_LANG_ICL_TITLE);
		View::assign('submenu', View::fetchTpl('icl/inc_submenu.php'), FALSE);
		View::assign('form',  View::fetchTpl('icl/'.$form.'.php'), FALSE);
		View::assign('body',  View::fetchTpl('icl/edit.php'), FALSE);
	}

	/**
	 * args
	 *
	 * @param Bool $is_sit
	 * @return Array
	 */
	private static function args($is_sit = false)
	{
		$type = $is_sit ? 'iclsit' : 'icl';
		$vals = array();
		foreach (Model\Icl::$fields[$type] as $key => $default)
		{
			$vals[$key] = Input::post($key, $default);
		}
		return $vals;
	}

	/**
	 * add Icl
	 *
	 * @param Bool $is_sit
	 * @return Array
	 */
	private static function add($is_sit = false)
	{
		Util::setMassage($id = Model\Icl::insert(self::args($is_sit), $is_sit));
		return $id;
	}

	/**
	 * update Icl
	 *
	 * @param Integer $id
	 * @param Bool $is_sit
	 * @return Array
	 */
	private static function update($id, $is_sit = false)
	{
		Util::setMassage(Model\Icl::update($id, self::args($is_sit)));
		return $id;
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
		$item = Model\Icl::fetch($id);
		if (empty($item)) Util::error('Icl not found');

		$r = false;
		if (
			$act == 'delete' && $item['trash'] == 0 ||
			$act == 'undelete' && $item['trash'] == 1
		)
		{
			$item['trash'] = ! $item['trash'];
			$r = Model\Icl::update($id, $item);
		}
		elseif ($act == 'purge' && $item['trash'] == 1)
		{
			$r = Model\Icl::purge($id);
		}

		$act = strtoupper($act);
		Util::setMassage($r);
		Util::redirect(A11YC_ICL_URL.'index');
	}
}
