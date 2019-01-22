<?php
/**
 * A11yc\Controller\Export\Result
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller\Export;

use A11yc\Model;

class Result
{
	/**
	 * export
	 *
	 * @return Void
	 */
	public static function export()
	{
		$vals = array();

		$vals['page'] = Model\Page::fetchAll();
		$vals['issue']  = Model\Issue::fetchAll();
		foreach ($vals['page'] as $page)
		{
			$vals['result'][$page['url']] = Model\Result::fetch($page['url']);
			$vals['check'][$page['url']]  = Model\Checklist::fetch($page['url']);
			$vals['html'][$page['url']] = Model\Html::fetch($page['url'], '', true, true);
		}

		View::assign('vals', json_encode($vals));
		View::assign('title', A11YC_LANG_PAGE_LABEL_EXPORT_CHECK_RESULT);
		View::assign('body', View::fetchTpl('export/resultexport.php'), FALSE);
	}

	/**
	 * import
	 *
	 * @return Void
	 */
	public static function import()
	{
		if (Input::isPostExists())
		{
			$results = Input::post('result', array());
			$results = json_decode($results, true);

			self::importPage($results);
			self::importResult($results);
			self::importChecklists($results);
			self::importIssue($results);
			self::importCache($results);
		}

		View::assign('title', A11YC_LANG_PAGE_LABEL_EXPORT_CHECK_RESULT);
		View::assign('body', View::fetchTpl('export/resultimport.php'), FALSE);
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
			foreach ($vals as $criterion => $val)
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
			Model\Html::insert($url, '', $val);
		}
	}

}
