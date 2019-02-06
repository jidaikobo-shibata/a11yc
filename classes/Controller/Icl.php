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
	 * read
	 *
	 * @return Void
	 */
	public static function actionRead()
	{
		static::read();
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

		$icl_ids = array();
		foreach ($icls as $criterion => $icl)
		{
			$iclssit_id = 0;
			foreach ($icl as $row)
			{
				$values = array(
					'criterion' => Util::code2key($criterion),
				);

				// iclsit - situation
				if ( ! is_array($row))
				{
					$values['title'] = $row;
					$iclssit_id = Model\Icl::insert($values, true);
					continue;
				}

				// icl
				$values = array_merge($row, $values);
				$values['situation'] = $iclssit_id;
				$values['title_short'] = Arr::get($values, 'title_short', $values['title']);
				$icl_ids[] = Model\Icl::insert($values, false);
			}
		}

		Model\Setting::update('icl', $icl_ids);
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
		View::assign('title',   A11YC_LANG_ICL_TITLE);
		View::assign('body',    View::fetchTpl('icl/implements_checklist.php'), false);
	}

	/**
	 * view
	 *
	 * @return Void
	 */
	public static function view()
	{
		View::assign('is_view', true);
		View::assign('title',   A11YC_LANG_ICL_TITLE);
		View::assign('body',    View::fetchTpl('icl/implements_checklist.php'), false);

		View::display(array(
				'inc_report_header.php',
				'body.php',
				'inc_report_footer.php',
			));
		exit();
	}

	/**
	 * read
	 *
	 * @return Void
	 */
	public static function read()
	{
		$id = Input::get('id', false);
		if ( ! $id) Util::redirect(A11YC_URL);

		View::assign('title', A11YC_LANG_ICL_TITLE);
		View::assign('item',  Model\Icl::fetch($id), false);
		View::assign('body',  View::fetchTpl('icl/read.php'), false);
	}

	/**
	 * edit
	 *
	 * @return Void
	 */
	public static function edit()
	{
		$id     = Input::get('id', false);
		$is_sit = Input::get('is_sit', false);
		$item   = $id ? Model\Icl::fetch($id) : array();
		$qstr   = '&amp;id=';
		$qstr   = $is_sit ? '&amp;is_sit=1'.$qstr : $qstr;

		// create or update
		if (Input::isPostExists())
		{
			$id = Input::post('is_add', false) ? self::add($is_sit) : self::update($id, $is_sit);
			Util::redirect(A11YC_ICL_URL.'edit'.$qstr.$id); // redirect to new id
		}
		$qstr.= $id; // action destination

		View::assign('item', $item);

		$form = $is_sit ? 'inc_edit_iclsit' :'inc_edit_icl';
		View::assign('is_add', ! $id);
		View::assign('qstr',   $qstr);
		View::assign('title',  A11YC_LANG_ICL_TITLE);
		View::assign('form',   View::fetchTpl('icl/'.$form.'.php'), false);
		View::assign('body',   View::fetchTpl('icl/edit.php'), false);
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
		return Model\Data::postfilter(Model\Icl::$fields[$type]);
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

		Util::setMassage($r);
		Util::redirect(A11YC_ICL_URL.'index');
	}
}
