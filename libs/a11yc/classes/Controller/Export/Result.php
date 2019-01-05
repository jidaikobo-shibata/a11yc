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
		$results = array(
			'pages' => array(),
			'results' => array(),
			'checklists' => array(),
			'issues' => array(),
		);
		$results['pages'] = Model\Pages::fetch();
		foreach ($results['pages'] as $page)
		{
			$sql = 'SELECT * FROM '.A11YC_TABLE_RESULTS.' WHERE `url` = ?'.Db::versionSql().';';
			$results['results'][] = Db::fetchAll($sql, array($page['url']));;

			$sql = 'SELECT * FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?'.Db::versionSql().';';
			$results['checklists'][] = Db::fetchAll($sql, array($page['url']));

			$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES.' WHERE `url` = ?;';
			$results['issues'][] = Db::fetchAll($sql, array($page['url']));
		}

		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUES.' WHERE (`is_common` = 1 OR `url` = "");';
		$results['issues'][] = Db::fetchAll($sql);

		$sql = 'SELECT * FROM '.A11YC_TABLE_CACHES.';';
		$results['htmls'] = Db::fetch($sql);
		foreach ($results['htmls'] as $k => $html)
		{
			if ( ! isset($html['data'])) continue;
			$results['htmls'][$k]['data'] = htmlspecialchars($html['data'], ENT_QUOTES);
		}

		$sql = 'SELECT * FROM '.A11YC_TABLE_ISSUESBBS.';';
		$results['issuebbs'] = Db::fetchAll($sql);

		View::assign('results', serialize($results));
		View::assign('title', A11YC_LANG_PAGES_LABEL_EXPORT_CHECK_RESULT);
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

		View::assign('title', A11YC_LANG_PAGES_LABEL_EXPORT_CHECK_RESULT);
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
		$results = unserialize($results);

		// import page
		$page_num = 0;
		if (isset($results['pages']))
		{
			$this_pages = Model\Pages::fetch();
			$this_pages = array_column($this_pages, 'url');
			foreach ($results['pages'] as $vals)
			{
				if (in_array($vals['url'], $this_pages)) continue;
				if (Model\Pages::insert($vals)) $page_num++;
			}
		}

		// import result
		$result_num = 0;
		foreach ($results['results'] as $pages)
		{
		foreach ($pages as $vals)
		{
			if (Model\Results::insert($vals)) $result_num++;
		}
		}

		// evaluate
		foreach ($results['pages'] as $vals)
		{
			Model\Pages::updateField(
				$vals['url'],
				'level',
				Evaluate::getLevelByUrl($vals['url'])
			);
		}



		// import checklists
		$checklists_num = 0;
		foreach ($results['checklists'] as $pages)
		{
		foreach ($pages as $vals)
		{
			if (Model\Checklist::insert($vals)) $checklists_num++;
		}
		}

		// import issue
		$issues_num = 0;
		foreach ($results['issues'] as $v)
		{
			if (empty($v)) continue;
			foreach ($v as $vals)
			{
				if ( ! isset($vals['id'])) continue;
				$old_id = $vals['id'];
				unset($vals['id']);
				if ($issue_id = Model\Issues::insert($vals))
				{
					$issues_num++;
					foreach ($results['issuebbs'] as $bbs)
					{
						if ($bbs['issue_id'] == $old_id)
						{
							unset($bbs['id']);
							$bbs['issue_id'] = $issue_id;
							Model\Issuesbbs::insert($bbs);
						}
					}
				}
			}
		}

		// import cache
		$results['htmls'];
		foreach ($results['htmls'] as $k => $vals)
		{
			if ( ! isset($vals['data'])) continue;
			$vals['data'] = htmlspecialchars_decode($vals['data']);
			Model\Html::insert($vals);
		}
	}
}
