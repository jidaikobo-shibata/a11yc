<?php
/**
 * A11yc\Controller\Process
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Process
{

	/**
	 * Show Index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		$yml = Yaml::fetch();
		View::assign('current', Model\Process::fetchAll());
		View::assign('processes', $yml['processes']);
		View::assign('pages', Model\Page::fetchAll());
		View::assign('title', '試験プロセス');
		View::assign('body',  View::fetchTpl('process/index.php'), FALSE);
	}

	/**
	 * discard
	 *
	 * @return Void
	 */
	public static function actionDiscard()
	{
		Model\Data::deleteByKey('process');
		Util::redirect(A11YC_URL.'?c=process&a=index');
	}

	/**
	 * Form
	 *
	 * @return Void
	 */
	public static function actionForm()
	{
		// check request: m
		$mode_or_url = Input::get('m', '');
		if ( ! in_array($mode_or_url, self::candidate())) Util::error('bad request.');

		// check request: p
		$yml = Yaml::fetch();
		$pcode = Input::get('p', '');
		if ( ! array_key_exists($pcode, $yml['processes'])) Util::error('bad request.');

		// dbio
		self::dbio($pcode, $mode_or_url);
		$current = Model\Process::fetch(Input::get('m', $mode_or_url), array(), true);
		$current = Arr::get($current, $pcode, array());

		View::assign('current', Arr::get($current, 'vals', array()));
		View::assign('p', $pcode);
		View::assign('m', $mode_or_url);
		View::assign('processes', $yml['processes'], false);
		View::assign('techs', $yml['techs']);
		View::assign('pages', Model\Page::fetchAll());
		View::assign('title', Util::key2code($pcode).': '.$yml['processes'][$pcode]['title']);
		View::assign('body',  View::fetchTpl('process/form.php'), FALSE);
	}

	/**
	 * dbio
	 *
	 * @param String $pcode
	 * @param String $mode_or_url
	 * @return Void
	 */
	private static function dbio($pcode, $mode_or_url)
	{
		if ( ! Input::isPostExists()) return;

		$value = array();
		$value[$pcode]['status'] = '';
		foreach (Input::postArr('vals') as $k => $v)
		{
			$value[$pcode]['vals'][$k] = Model\Data::filter($v, Model\Process::$each_fields);
		}
		if ($mode_or_url == 'common')
		{
			self::updateCommon($pcode, $value);
			return;
		}
		self::update($pcode, $mode_or_url, $value);
	}

	/**
	 * candidate
	 *
	 * @return Array
	 */
	private static function candidate()
	{
		$pages = array('common');
		foreach (Model\Page::fetchAll() as $v)
		{
			$pages[] = $v['url'];
		}
		return $pages;
	}

	/**
	 * update common
	 *
	 * @param String $pcode
	 * @param Array $value
	 * @return Void
	 */
	private static function updateCommon($pcode, $value)
	{
		foreach (self::candidate() as $v)
		{
			self::update($pcode, $v, $value, true);
		}
	}

	/**
	 * update
	 *
	 * @param String $pcode
	 * @param String $url
	 * @param Array $value
	 * @param Bool $is_common
	 * @return Void
	 */
	private static function update($pcode, $url, $value, $is_common = false)
	{
		$current = Model\Process::fetch($url, array(), true);
		$status = $is_common ? 'common' : 'done' ;
		$current_status = Arr::get(Arr::get($current, $pcode, array()), 'status', '');
		$value[$pcode]['status'] = $current_status == 'done' ? 'done' : $status;

		// insert
		if (empty($current))
		{
			Model\Process::insert($url, $value);
			return;
		}

		// update
		// don't change status which test was done
		Model\Process::update($url, $value);
	}
}
