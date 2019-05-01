<?php
/**
 * A11yc\Controller\DownloadIssue
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait DownloadIssue
{
	/**
	 * issue
	 *
	 * @return Void
	 */
	public static function issue()
	{
		$issues = Model\Issue::fetchByStatus(0);
		$settings = Model\Setting::fetchAll();
		$pages = Model\Page::fetchAll();

		$vals = array();
		$vals['commons'] = array();
		foreach ($pages as $v)
		{
			$vals[$v['url']] = array();
		}

		foreach ($issues as $url => $criterions)
		{
			foreach ($criterions as $v)
			{
				foreach ($v as $val)
				{
					if ($val['output'] === false) continue;
					$vals[$url][] = $val;
				}
			}
			$vals[$url] = Util::multisort($vals[$url], 'seq');
		}

		$titles = array_column($pages, 'title', 'url');
		if ( ! isset($titles['commons']))
		{
			$titles['commons'] = A11YC_LANG_ISSUE_IS_COMMON;
		}

		$serial_nums = array_column($pages, 'serial_num', 'url');

		View::assign('serial_nums', $serial_nums);
		View::assign('titles', $titles);
		View::assign('images', array_column($pages, 'image_path', 'url'));
		View::assign('issues', $vals);
		View::assign('settings', $settings);
		View::assign('title', $settings['client_name'].' - '.A11YC_LANG_ISSUE_REPORT_HEAD_SUFFIX);
		View::assign('body', View::fetchTpl('download/issue.php'), FALSE);
		File::download('issue_report.html', View::fetch('body'));
	}
}
