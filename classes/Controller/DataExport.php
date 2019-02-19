<?php
/**
 * A11yc\Controller\DataExport
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DataExport
{
	/**
	 * export
	 *
	 * @return Void
	 */
	public static function export()
	{
		if (Input::isPostExists())
		{
			static::execute();
		}

		View::assign('title', A11YC_LANG_EXPORT);
		View::assign('body', View::fetchTpl('data/export.php'), FALSE);
	}

	/**
	 * execute
	 *
	 * @return Void
	 */
	public static function execute()
	{
		$vals = array();
		$versions = array(0);
		$targets = Input::postArr('targets');

		if (empty($targets)) return;

		if (in_array('site', $targets))
		{
			$vals['version'] = Model\Version::fetchAll();
			$versions = array_merge($versions, array_keys($vals['version']));
			$vals['base_url'] = Model\Data::baseUrl();
		}
		$vals['version_keys'] = $versions;

		foreach ($versions as $version)
		{
			Model\Version::setVersion($version);
			$each = array();

			$each = self::exportIcl($each, $targets);
			$each = self::exportSetting($each, $targets);
			$each = self::exportPage($each, $targets);
			$each = self::exportIssue($each, $targets);
			$each = self::exportResult($each, $targets);

			$vals[$version] = $each;
		}

		Model\Version::setVersion(0);
		File::download('a11yc.export.json', json_encode($vals));
	}

	/**
	 * Icl
	 *
	 * @param $vals
	 * @param $targets
	 * @return Array
	 */
	public static function exportIcl($vals, $targets)
	{
		if (in_array('icl', $targets))
		{
			$vals['icl'] = Model\Icl::fetchAll();
		}
		return $vals;
	}

	/**
	 * Setting
	 *
	 * @param $vals
	 * @param $targets
	 * @return Array
	 */
	public static function exportSetting($vals, $targets)
	{
		$setting = Model\Setting::fetchAll();

		if (in_array('bulk', $targets))
		{
			$tmp = array();
			$tmp['bulk_checks'] = Arr::get($setting, 'bulk_checks', array());
			$tmp['bulk_results'] = Arr::get($setting, 'bulk_results', array());
			$tmp['bulk_iclchks'] = Arr::get($setting, 'bulk_iclchks', array());
			$vals['setting'] = array_merge(Arr::get($vals, 'setting', array()), $tmp);
		}

		if (in_array('icl', $targets))
		{
			$tmp = array();
			$tmp['icl'] = Arr::get($setting, 'icl', array());
			$vals['setting'] = array_merge(Arr::get($vals, 'setting', array()), $tmp);
		}

		if (in_array('setting', $targets))
		{
			$vals['setting'] = Model\Setting::fetchAll();
		}
		return $vals;
	}

	/**
	 * Page
	 *
	 * @param $vals
	 * @param $targets
	 * @return Array
	 */
	public static function exportPage($vals, $targets)
	{
		if (in_array('page', $targets))
		{
			$vals['page']  = Model\Page::fetchAll();
			foreach (Model\Page::fetchAll() as $page)
			{
				$vals['html'][$page['url']]   = Model\Html::fetch($page['url']);
			}
		}
		return $vals;
	}

	/**
	 * Issue
	 *
	 * @param $vals
	 * @param $targets
	 * @return Array
	 */
	public static function exportIssue($vals, $targets)
	{
		if (in_array('issue', $targets))
		{
			$vals['issue'] = Model\Issue::fetchAll();
		}
		return $vals;
	}

	/**
	 * Result
	 *
	 * @param $vals
	 * @param $targets
	 * @return Array
	 */
	public static function exportResult($vals, $targets)
	{
		foreach (Model\Page::fetchAll() as $page)
		{
			if (in_array('result', $targets))
			{
				$vals['result'][$page['url']] = Model\Result::fetch($page['url']);
			}

			if (in_array('check', $targets))
			{
				$vals['check'][$page['url']]  = Model\Checklist::fetch($page['url']);
			}
		}
		return $vals;
	}

}
