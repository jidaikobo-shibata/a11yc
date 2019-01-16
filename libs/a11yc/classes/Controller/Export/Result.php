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
			static::dbio();
		}

		View::assign('title', A11YC_LANG_PAGE_LABEL_EXPORT_CHECK_RESULT);
		View::assign('body', View::fetchTpl('export/resultimport.php'), FALSE);
	}

	/**
	 * dbio
	 *
	 * @return Void
	 */
	private static function dbio()
	{
		$results = Input::post('result');
		if (empty($results)) return;
		$results = json_decode($results, true);

		// import page
		if (isset($results['page']))
		{
			$this_pages = Model\Page::fetchAll();
			$this_pages = array_column($this_pages, 'url');
			foreach ($results['page'] as $vals)
			{
				if (in_array($vals['url'], $this_pages)) continue;
				Model\Page::insert($vals['url'], $vals);
			}
		}

		// import result
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



		// import checklists
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

		// import issue
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

		// import cache
		foreach ($results['html'] as $url => $val)
		{
			if ( ! isset($val)) continue;
			$val = htmlspecialchars_decode($val);
			Model\Html::insert($url, '', $val);
		}
	}
}
