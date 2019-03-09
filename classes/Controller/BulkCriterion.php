<?php
/**
 * A11yc\Controller\BulkCriterion
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait BulkCriterion
{
	private static $titles = array(
		"result" => A11YC_LANG_BULK_CRITERION_TITLE,
		"icl" => A11YC_LANG_BULK_ICL_TITLE,
		"check" => A11YC_LANG_BULK_ICL_TECH_TITLE,
		"failure" => A11YC_LANG_BULK_ICL_FAILURE_TITLE,
	);

	/**
	 * criterion
	 *
	 * @return Void
	 */
	public static function criterion()
	{
		// value check
		$criterions = Yaml::each('criterions');
		$criterion = Input::param('criterion', '');
		if ( ! array_key_exists($criterion, $criterions)) Util::error('wrong criterion');

		$focus = Input::param('focus', '');
		if ( ! array_key_exists($focus, self::$titles)) Util::error('wrong focus');

		// dbio
		if (Input::isPostExists())
		{
			static::criterionDbio($criterion);
		}

		// pages
		$pages = Model\Page::fetchAll();
		foreach ($pages as $k => $v)
		{
			$pages[$k]['results'] = Model\Result::fetch($v['url']);
			$pages[$k]['iclchks'] = Model\Iclchk::fetch($v['url']);
			$pages[$k]['issues']  = Model\Issue::fetch($v['url']);
			$pages[$k]['cs']      = Model\Checklist::fetch($v['url']);
		}

		$refs = Values::getRefUrls();
		$settings = Model\Setting::fetchAll();
		$standards = Yaml::each('standards');
		$standard = Arr::get($settings, 'standard', 0);
		View::assign('focus',        Input::get('focus', ''));
		View::assign('is_new',       false);
		View::assign('refs',         $refs[$standard]);
		View::assign('current_user', Users::fetchCurrentUser());
		View::assign('users',        Users::fetchUsersOpt());
		View::assign('pages',        $pages);
		View::assign('yml',          Yaml::fetch());
		View::assign('criterion',    $criterion);

		$template = Input::get('integrate', false) ? 'criterion_integrate' : 'criterion' ;
		$title = self::$titles[$focus];
		$title.= Input::get('integrate', false) ? ' ('.A11YC_LANG_INTEGRATE.')' : '' ;
		View::assign('body',  View::fetchTpl('bulk/'.$template.'.php'), FALSE);
		View::assign('title', $title);
	}

	/**
	 * criterionDbio
	 *
	 * @param String $criterion
	 * @return Void
	 */
	public static function criterionDbio($criterion)
	{
		$results = Input::postArr('results', NULL);
		$iclchks = Input::postArr('iclchks', NULL);
		$chk     = Input::postArr('chk', NULL);
		$pages   = Model\Page::fetchAll();

		$success = 0;
		$fail = 0;
		foreach ($pages as $v)
		{
			if (Input::get('integrate', false))
			{
				if (self::updateIntegrate($criterion, $results, $iclchks, $chk, $v))
				{
					$success++;
				}
				else
				{
					$fail++;
				}
				continue;
			}
			if (self::updateEach($criterion, $results, $iclchks, $chk, $v))
			{
				$success++;
			}
			else
			{
				$fail++;
			}
		}

		if ($success)
		{
			Session::add('messages', 'messages', sprintf(A11YC_LANG_UPDATE_BULK_SUCCEED, $success));
		}
		if ($fail)
		{
			Session::add('messages', 'errors', sprintf(A11YC_LANG_UPDATE_BULK_FAILED, $fail));
		}
	}

	/**
	 * updateEach
	 *
	 * @param String $criterion
	 * @param Array $results
	 * @param Array $iclchks
	 * @param Array $chk
	 * @param Array $v
	 * @return Integer|Bool
	 */
	private static function updateEach($criterion, $results, $iclchks, $chk, $v)
	{
		if (isset($results[$v['dbid']]))
		{
			$vals = array_merge(Model\Result::fetch($v['url']), $results[$v['dbid']]);
			return Model\Result::update($v['url'], $vals);
		}

		if (isset($iclchks[$v['dbid']]))
		{
			$vals = array_replace(Model\Iclchk::fetch($v['url']), $iclchks[$v['dbid']]);
			return Model\Iclchk::update($v['url'], $vals);
		}

		if (isset($chk[$v['dbid']]))
		{
			$vals = array_replace(Model\Checklist::fetch($v['url']), $chk[$v['dbid']]);
			return Model\Checklist::update($v['url'], $chk[$v['dbid']]);
		}
	}

	/**
	 * updateIntegrate
	 *
	 * @param String $criterion
	 * @param Array $results
	 * @param Array $iclchks
	 * @param Array $chk
	 * @param Array $v
	 * @return Integer|Bool
	 */
	private static function updateIntegrate($criterion, $results, $iclchks, $chk, $v)
	{
		if (isset($results))
		{
			$vals = array_merge(Model\Result::fetch($v['url']), $results[0]);
			return Model\Result::update($v['url'], $vals);
		}

		$icltree = Model\icl::fetchTree(true);

		if (isset($iclchks))
		{
			// TODO mikansei
			$vals = array_replace(Model\Iclchk::fetch($v['url']), $iclchks[0]);
			return Model\Iclchk::update($v['url'], $vals);
		}

		if (isset($chk))
		{
			$current = Model\Checklist::fetch($v['url']);
			$keys = array();
			foreach ($icltree[$criterion] as $vv)
			{
				$keys = array_merge($keys, $vv);
			}

			foreach ($keys as $key)
			{
				if (isset($current[$key])) unset($current[$key]);
			}

			$vals = array_replace($current, $chk[0]);
			return Model\Checklist::update($v['url'], $vals);
		}
	}
}
