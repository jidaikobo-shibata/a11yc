<?php
/**
 * A11yc\Controller\Issues\Index
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller\Issues;

use A11yc\Model;

class Index
{
	/**
	 * issue types
	 *
	 * @return Void
	 */
	private static function assignIssueTypes()
	{
		$issues = array(
			'yet'      => Model\Issues::fetchByStatus(0),
			'progress' => Model\Issues::fetchByStatus(1),
			'done'     => Model\Issues::fetchByStatus(2),
			'trash'    => Model\Issues::fetchByStatus(3),
		);
		View::assign('issues',   $issues);
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
		View::assign('title',    A11YC_LANG_ISSUES_TITLE.A11YC_LANG_ISSUES_STATUS);
		View::assign('body',     View::fetchTpl('issues/index_failures.php'), FALSE);
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

		View::assign('items',    Model\Issues::fetchByStatus($issue_type));
		View::assign('title',    constant('A11YC_LANG_ISSUES_TITLE_'.strtoupper($keys[$issue_type])));
		View::assign('body',     View::fetchTpl('issues/index_any.php'), FALSE);
	}
}
