<?php
/**
 * A11yc\Controller\Bulk
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

class Bulk extends Checklist
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionIndex()
	{
		static::check('bulk');
	}

	/**
	 * dbio - override
	 *
	 * @param String $url
	 * @return Void
	 */
	public static function dbio($url)
	{
		if ($url != 'bulk') Util::error();

		if (Input::isPostExists())
		{
			// update default only
			self::setDefault();

			// update all
			if (Input::post('update_all') == 1) return;

			// update all
			self::all();
		}
	}

	/**
	 * set Default
	 *
	 * @return Void
	 */
	public static function setDefault()
	{
		// results
		Model\Setting::delete('bulk_results');

		$value = array();
		foreach (Input::postArr('results') as $criterion => $v)
		{
			$value[$criterion]['memo']   = stripslashes($v['memo']);
			$value[$criterion]['uid']    = intval(Arr::get($v, 'uid', 0));
			$value[$criterion]['result'] = intval(Arr::get($v, 'result', 0));
			$value[$criterion]['method'] = intval(Arr::get($v, 'method', 0));
		}
		Model\Setting::insert(array(
				'bulk_results' => $value,
			));

		// delete all
		Model\Setting::delete('bulk_checks');

		// insert
		$chks = Input::postArr('chk');
		$r = Model\Setting::insert(array(
				'bulk_checks' => $chks,
			));

		if ($r === true || empty($chks))
		{
			Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			return;
		}

		Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
	}

	/**
	 * update all
	 *
	 * @return Void
	 */
	private static function all()
	{
		// update all except for in trash item
		foreach (Model\Page::fetchAll() as $v)
		{
			$url = $v['url'];

			// results
			self::updateResults($url);

			// checks
			self::updateChecks($url);

			// update each page
			$update_done = intval(Input::post('update_done'));
			$date = date('Y-m-d');

			// do not update done flag
			if ($update_done == 1)
			{
				Model\Page::updatePartial($url, 'date', $date);
			}
			else
			{
				// update done flag done or not done
				$done = $update_done == 2 ? 1 : 0 ;
				Model\Page::updatePartial($url, 'date', $date);
				Model\Page::updatePartial($url, 'done', $done);
			}

			// update level
			Model\Page::updatePartial($url, 'level', Evaluate::getLevelByUrl($url));
		}
	}

	/**
	 * update results
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateResults($url)
	{
		$bulk = Input::postArr('results');
		$vals = array();

		if (Input::post('update_all') == 2)
		{
			$current = Model\Result::fetch($url);
			foreach ($bulk as $criterion =>$v)
			{
				foreach (Model\Result::$fields as $key => $default)
				{
					$vals[$criterion][$key] = Arr::get($current[$criterion], $key) != $default ?
																	Arr::get($current[$criterion], $key) :
																	$v[$key];
				}
			}
		}
		else
		{
			$vals = $bulk;
		}
		Model\Result::update($url, $vals);
	}

	/**
	 * update checks
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateChecks($url)
	{
		$bulk = Input::postArr('chk');
		$vals = array();

		if (Input::post('update_all') == 2)
		{
			$current = Model\Checklist::fetch($url);

			foreach ($bulk as $criterion =>$v)
			{
				$vals[$criterion] = array_merge(Arr::get($current, $criterion, array()), $v);
			}
		}
		else
		{
			$vals = $bulk;
		}
		Model\Checklist::update($url, $vals);
	}
}
