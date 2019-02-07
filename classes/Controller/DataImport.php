<?php
/**
 * A11yc\Controller\DataImport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DataImport
{
	/**
	 * all
	 *
	 * @param Bool $is_icl
	 * @return Void
	 */
	public static function import($is_icl = false)
	{
		if (Input::isPostExists())
		{
			$file = Input::file('import');
			if ( ! file_exists(Arr::get($file, 'tmp_name'))) Util::redirect(A11YC_URL);
			$results = json_decode(file_get_contents($file['tmp_name']), true);

			if ( ! $is_icl)
			{
				$is_add = self::addNewSite($results);
				self::importSetting($results, $is_add);
				self::importPage($results);
				self::importResult($results);
				self::importChecklist($results);
				self::importIssue($results);
				self::importCache($results);
			}
			self::importIcl($results);
		}

		View::assign('is_icl', $is_icl);
		$title = $is_icl ? A11YC_LANG_ICL_TITLE_IMPORT : A11YC_LANG_PAGE_LABEL_IMPORT_CHECK_RESULT ;
		View::assign('title', $title);
		$describe = $is_icl ? A11YC_LANG_PAGE_LABEL_IMPORT_ICL_EXP : A11YC_LANG_PAGE_LABEL_IMPORT_CHECK_RESULT_EXP ;
		View::assign('describe', $describe);
		View::assign('body', View::fetchTpl('data/import.php'), FALSE);
	}

	/**
	 * add new site
	 *
	 * @param Array $results
	 * @return Bool
	 */
	private static function addNewSite($results)
	{
		if ( ! isset($results['base_url'])) return false;

		// add new site
		$new_site = $results['base_url'];
		$sites = Model\Data::fetchSites();

		if (in_array($new_site, $sites))
		{
			Session::add('messages', 'errors', A11YC_LANG_CTRL_ALREADY_EXISTS);
			Util::redirect(A11YC_URL);
		}
		$sites[] = Util::urldec($new_site);
		Model\Data::update('sites', 'global', $sites);
		$group_id = max(array_keys($sites));
		Model\Data::update('group_id', 'global', $group_id, 0, 1);
		Model\Data::setGroupId($group_id);

		return true;
	}

	/**
	 * setting
	 *
	 * @param Array $results
	 * @param Bool $is_add
	 * @return Void
	 */
	private static function importSetting($results, $is_add)
	{
		if ( ! isset($results['setting']) || ! $is_add) return;
		Model\Setting::updateAll($results);
	}

	/**
	 * page
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importPage($results)
	{
		if ( ! isset($results['page'])) return;

		$this_pages = Model\Page::fetchAll();
		$this_pages = array_column($this_pages, 'url');
		foreach ($results['page'] as $vals)
		{
			if (in_array($vals['url'], $this_pages)) continue;
			Model\Page::insert($vals['url'], $vals);
		}
	}

	/**
	 * result
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importResult($results)
	{
		if ( ! isset($results['result'])) return;

		foreach ($results['result'] as $url => $vals)
		{
			$ins = Model\Result::fetch($url);
			foreach ($vals as $criterion => $val)
			{
				if ( ! isset($ins[$criterion]) || empty($ins[$criterion]))
				{
					$ins[$criterion] = $val;
				}
			}
			Model\Result::insert($url, $vals);
		}

		// evaluate
		foreach ($results['page'] as $vals)
		{
			$url = $vals['url'];
			Model\Page::updatePartial($url, 'level', Evaluate::getLevelByUrl($url));
		}
	}

	/**
	 * checklist
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importChecklist($results)
	{
		if ( ! isset($results['check'])) return;

		foreach ($results['check'] as $url => $vals)
		{
			$ins = Model\Checklist::fetch($url);
			foreach ($vals as $criterion => $val)
			{
				if (isset($ins[$criterion]))
				{
					$ins[$criterion] = array_merge($ins[$criterion], $val);
					continue;
				}
				$ins[$criterion] = $val;
			}
			Model\Checklist::update($url, $ins);
		}
	}

	/**
	 * issue
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importIssue($results)
	{
		if ( ! isset($results['issue'])) return;

		foreach ($results['issue'] as $url => $vals)
		{
			if (empty($vals)) continue;
			foreach ($vals as $val)
			{
				foreach ($val as $v)
				{
					Model\Issue::insert($url, $v);
				}
			}
		}
	}

	/**
	 * cache
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importCache($results)
	{
		if ( ! isset($results['html'])) return;

		foreach ($results['html'] as $url => $val)
		{
			if ( ! isset($val)) continue;
			$val = htmlspecialchars_decode($val);
			Model\Html::insert($url, $val);
		}
	}

	/**
	 * icl
	 *
	 * @param Array $results
	 * @return Void
	 */
	private static function importIcl($results)
	{
		if ( ! isset($results['icl'])) return;

		Model\Data::deleteByKey('icl');
		Model\Data::deleteByKey('iclsit');
		Model\Setting::update('icl', array());

		$chks = array();
		foreach ($results['icl'] as $v)
		{
			$chks[] = Model\Icl::insert($v, $v['is_sit']);
		}
		Model\Setting::update('icl', $chks);
	}
}
