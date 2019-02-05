<?php
/**
 * A11yc\Controller\IssueIndex
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;

trait IssueIndex
{
	/**
	 * issue types
	 *
	 * @return Void
	 */
	private static function assignIssueTypes()
	{
		$issues = array(
			'yet'      => Model\Issue::fetchByStatus(0),
			'progress' => Model\Issue::fetchByStatus(1),
			'done'     => Model\Issue::fetchByStatus(2),
			'trash'    => Model\Issue::fetchByStatus(3),
		);
		View::assign('issues', $issues);
	}

	/**
	 * failures - normal index
	 *
	 * @return Void
	 */
	public static function failures()
	{
		self::assignIssueTypes();
		View::assign('yml',      Yaml::each('techs'));
		View::assign('failures', Model\Checklist::fetchFailures());
		View::assign('title',    A11YC_LANG_ISSUE_TITLE.A11YC_LANG_ISSUE_STATUS);
		View::assign('body',     View::fetchTpl('issue/index_failures.php'), FALSE);
	}

	/**
	 * any
	 *
	 * @param Integer $issue_type
	 * @return Void
	 */
	public static function any($issue_type)
	{
		self::assignIssueTypes();

		$issues = View::fetch('issues');
		$keys = array_keys($issues);

		View::assign('items', Model\Issue::fetchByStatus($issue_type));
		View::assign('title', constant('A11YC_LANG_ISSUE_TITLE_'.strtoupper($keys[$issue_type])));
		View::assign('body',  View::fetchTpl('issue/index_any.php'), FALSE);
	}
}
