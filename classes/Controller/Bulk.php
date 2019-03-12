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
	use BulkCriterion;

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
	 * action criterion
	 *
	 * @return Void
	 */
	public static function actionCriterion()
	{
		// use BulkCriterion
		static::criterion();
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

		$results = Input::postArr('results');
		$value = array();
		foreach ($results[0] as $criterion => $v)
		{
			$value[$criterion]['memo']   = stripslashes($v['memo']);
			$value[$criterion]['uid']    = intval(Arr::get($v, 'uid', 0));
			$value[$criterion]['result'] = intval(Arr::get($v, 'result', 0));
			$value[$criterion]['method'] = intval(Arr::get($v, 'method', 0));
		}
		Model\Setting::insert(array(
				'bulk_results' => $value,
			));

		// iclchk
		Model\Setting::delete('bulk_iclchks');
		$chks = Input::postArr('iclchks');
		Model\Setting::insert(array(
				'bulk_iclchks' => Arr::get($chks, 0, array()),
			));

		// checks
		Model\Setting::delete('bulk_checks');
		$chks = Input::postArr('chk');
		$r = Model\Setting::insert(array(
				'bulk_checks' => Arr::get($chks, 0, array()),
			));

		if ($r === true || empty(Arr::get($chks, 0, array())))
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

			// icls
			self::updateIcls($url);

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
		$current = Model\Result::fetch($url);

		if (Input::post('update_all') == 2 &&  ! empty($current))
		{
			foreach (Arr::get($bulk, 0, array()) as $criterion =>$v)
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
			$vals = Arr::get($bulk, 0, array());
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
		$loops = array(
			Model\Icl::fetchAll(),
			Yaml::each('criterions'),
		);

		$current = Model\Checklist::fetch($url);
		if (Input::post('update_all') == 2 && ! empty($current))
		{
			foreach ($loops as $v)
			{
				foreach (array_keys($v) as $id)
				{
					$vals[$id] = array_merge(
						Arr::get($current, $id, array()),
						Arr::get(Arr::get($bulk, 0, array()), $id, array())
					);
					$vals[$id] = array_unique($vals[$id]);
					if (empty($vals[$id])) unset($vals[$id]);
				}
			}
		}
		else
		{
			$vals = Arr::get($bulk, 0, array());
		}
		Model\Checklist::update($url, $vals);
	}

	/**
	 * update icl
	 *
	 * @param String $url
	 * @return Void
	 */
	private static function updateIcls($url)
	{
		$bulk = Input::postArr('iclchks');

		$vals = array();

		$current = Model\Iclchk::fetch($url);
		$is_done = count(array_unique($current)) > 1;

		if (Input::post('update_all') == 2 && $is_done)
		{
			foreach (Arr::get($bulk, 0, array()) as $iclid => $v)
			{
				if (isset($current[$iclid])) continue;
				$vals[$iclid] = $v;
			}
		}
		else
		{
			$vals = Arr::get($bulk, 0, array());
		}

		if ($is_done)
		{
			Model\Iclchk::update($url, $vals);
			return;
		}

		Model\Iclchk::insert($url, $vals);
	}
}
