<?php
/**
 * A11yc\Values
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

use A11yc\Model;

class Values
{
	/**
	 * get selection methods
	 *
	 * @return Array
	 */
	public static function selectedMethods()
	{
		return array(
			0 => A11YC_LANG_CANDIDATES0,
			1 => A11YC_LANG_CANDIDATES1,
			2 => A11YC_LANG_CANDIDATES2,
			3 => A11YC_LANG_CANDIDATES3,
			4 => A11YC_LANG_CANDIDATES4,
		);
	}

	/**
	 * get selection reasons
	 *
	 * @return Array
	 */
	public static function selectionReasons()
	{
		return array(
//			0 => '-',
			1 => A11YC_LANG_CANDIDATES_IMPORTANT,
			2 => A11YC_LANG_CANDIDATES_RANDOM,
			3 => A11YC_LANG_CANDIDATES_ALL,
			4 => A11YC_LANG_CANDIDATES_PAGEVIEW,
			5 => A11YC_LANG_CANDIDATES_NEW,
			6 => A11YC_LANG_CANDIDATES_ETC,
		);
	}

	/**
	 * filter selection reasons in context
	 *
	 * @return Array
	 */
	public static function filteredSelectionReasons()
	{
		$selection_reasons = Values::selectionReasons();

		switch (Arr::get(Model\Setting::fetchAll(), 'selected_method'))
		{
			case 0: // not site unit
				$selection_reasons = array(6 => $selection_reasons[6]);
				break;
			case 1: // all
				$selection_reasons = array(3 => $selection_reasons[3]);
				break;
			case 2: // random
				$selection_reasons = array(2 => $selection_reasons[2]);
				break;
			case 3: // representative
				$selection_reasons = array(1 => $selection_reasons[1]);
				break;
			case 4: // representative and other pages
				unset($selection_reasons[3]);
				break;
		}

		return $selection_reasons;
	}

	/**
	 * get User Agent
	 *
	 * @return Array
	 */
	public static function uas()
	{
		return array(
			'using' => array(
				'name' => A11YC_LANG_UA_USING,
				'str' => '',
			),
			'iphone' => array(
				'name' => A11YC_LANG_UA_IPHONE,
				'str' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0 like Mac OS X) AppleWebKit/602.1.38 (KHTML, like Gecko) Version/10.0 Mobile/14A300 Safari/602.1',
			),
			'android' => array(
				'name' => A11YC_LANG_UA_ANDROID,
				'str' => 'Mozilla/5.0 (Linux; U; Android 2.3.3; ja-jp; INFOBAR A01 Build/S7142) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
			),
			'ipad' => array(
				'name' => A11YC_LANG_UA_IPAD,
				'str' => 'Mozilla/5.0 (iPad; CPU OS 10_0 like Mac OS X) AppleWebKit/602.1.38 (KHTML, like Gecko) Version/10.0 Mobile/14A300 Safari/602.1',
			),
			'tablet' => array(
				'name' => A11YC_LANG_UA_ANDROID_TABLET,
				'str' => 'Mozilla/5.0 (Android; Tablet; rv:36.0) Gecko/36.0 Firefox/36.0',
			),
			'featurephone' => array(
				'name' => A11YC_LANG_UA_FEATUREPHONE,
				'str' => 'DoCoMo/2.0 SH06A3(c500;TC;W30H18)',
			),
		);
	}

	/**
	 * target mime types
	 *
	 * @return Array
	 */
	public static function targetMimes()
	{
		return array(
			'text/html',
			'application/pdf',
		);
	}

	/**
	 * techsTypes
	 *
	 * @return Array
	 */
	public static function techsTypes()
	{
		return array(
			'G', // general
			'H', // HTML/XHTML
			'C', // CSS
			'SCR', // client side script
			'SVR', // server side script
//				'SMIL', // Synchronized Multimedia Integration Language
			'T', // plain text
			'ARIA', // ARIA
//				'FLASH', // Flash
//				'SL', // Silverlight
			'PDF', // PDF
			'F', // failure
		);
	}

	/**
	 * results options
	 *
	 * @return Array
	 */
	public static function resultsOptions()
	{
		return array(
			0 => A11YC_LANG_NOT_CHECKED,
			1 => A11YC_LANG_EXIST_NON.A11YC_LANG_PASS,
			2 => A11YC_LANG_PASS,
			-1 => A11YC_LANG_PASS_NON,
		);
	}

	/**
	 * icl options
	 *
	 * @return Array
	 */
	public static function iclOptions()
	{
		return array(
			1 => A11YC_LANG_EXIST_NON,
			2 => A11YC_LANG_PASS,
			-1 => A11YC_LANG_PASS_NON,
		);
	}

	/**
	 * test methods options
	 *
	 * @return Array
	 */
	public static function testMethodsOptions()
	{
		return array(
			0 => A11YC_LANG_NOT_CHECKED,
			1 => A11YC_LANG_TEST_METHOD_AC,
			2 => A11YC_LANG_TEST_METHOD_AF,
			3 => A11YC_LANG_TEST_METHOD_HC,
		);
	}

	/**
	 * get issue status
	 *
	 * @return Array
	 */
	public static function issueStatus()
	{
		return array(
			A11YC_LANG_ISSUE_STATUS_1,
			A11YC_LANG_ISSUE_STATUS_2,
			A11YC_LANG_ISSUE_STATUS_3,
		);
	}

	/**
	 * get reference urls according to standard
	 *
	 * @return Array
	 */
	public static function getRefUrls()
	{
		return array(
			// WCAG 2.0 - JIS X 8341-3:2016
			0 => array(
				'w' => A11YC_REF_WCAG20_URL,
				'u' => A11YC_REF_WCAG20_UNDERSTANDING_URL,
				't' => A11YC_REF_WCAG20_TECH_URL,
			),
			// ISO/IEC 40500:2012 - same as WCAG 2.0
			1 => array(
				'w' => A11YC_REF_WCAG20_URL,
				'u' => A11YC_REF_WCAG20_UNDERSTANDING_URL,
				't' => A11YC_REF_WCAG20_TECH_URL,
			),
		);
	}

	/**
	 * get types
	 *
	 * @return Array
	 */
	public static function getTypes()
	{
		return array(
			'html' => 1,
			'pdf' => 2,
		);
	}

	/**
	 * get machine check status
	 *
	 * @return Array
	 */
	public static function machineCheckStatus()
	{
		return array(
			-1 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_FAILED,
			1 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_DONE,
			2 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_PASSED,
			3 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_EXIST,
			4 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_NONEXIST,
			5 => A11YC_LANG_CHECKLIST_MACHINE_CHECK_SKIPED,
		);
	}

	/**
	 * non use techs candidates
	 *
	 * @return Array
	 */
	public static function nonUseTechsCandidates()
	{

		return array (
			'G5', 'G17', 'G54', 'G56', 'G65', 'G70', 'G71', 'G75', 'G76', 'G79',
			'G81', 'G86', 'G101', 'G103', 'G105', 'G110', 'G112', 'G127', 'G128',
			'G136', 'G139', 'G141', 'G150', 'G151', 'G153', 'G156', 'G157', 'G160',
			'G163', 'G169', 'G172', 'G175', 'G181', 'G188', 'G190', 'G192', 'G193',
			'G194', 'G199', 'G200', 'G201', 'G203', 'G204', 'G205', 'G206', 'H34',
			'H40', 'H45', 'H46', 'H54', 'H56', 'H59', 'H60', 'H62', 'H76', 'H89',
			'H95', 'H96', 'H97', 'C17', 'C18', 'C19', 'C20', 'C21', 'C23', 'C24',
			'C25', 'C29', 'SCR14', 'SCR28', 'SCR29', 'SCR34', 'SCR36', 'SCR38',
			'SVR1', 'SVR2', 'SVR3', 'SVR4', 'SVR5', 'ARIA2', 'ARIA4', 'ARIA5',
			'ARIA6', 'ARIA7', 'ARIA8', 'ARIA9', 'ARIA10', 'ARIA11', 'ARIA12',
			'ARIA13', 'ARIA14', 'ARIA15', 'ARIA16', 'ARIA17', 'ARIA18', 'ARIA19',
			'ARIA20', 'ARIA21', 'PDF1', 'PDF2', 'PDF3', 'PDF4', 'PDF5', 'PDF6',
			'PDF7', 'PDF8', 'PDF9', 'PDF10', 'PDF11', 'PDF12', 'PDF13', 'PDF14',
			'PDF15', 'PDF16', 'PDF17', 'PDF18', 'PDF19', 'PDF20', 'PDF21', 'PDF22',
			'PDF23', 'F1', 'F2', 'F3', 'F4', 'F7', 'F8', 'F9', 'F10', 'F12', 'F13',
			'F14', 'F15', 'F16', 'F19', 'F20', 'F22', 'F23', 'F24', 'F25', 'F26',
			'F30', 'F31', 'F32', 'F33', 'F34', 'F36', 'F37', 'F38', 'F39', 'F40',
			'F41', 'F42', 'F43', 'F44', 'F46', 'F47', 'F48', 'F49', 'F50', 'F52',
			'F54', 'F55', 'F58', 'F59', 'F60', 'F61', 'F63', 'F65', 'F66', 'F67',
			'F68', 'F69', 'F70', 'F71', 'F72', 'F73', 'F74', 'F75', 'F77', 'F78',
			'F79', 'F80', 'F81', 'F82', 'F83', 'F84', 'F85', 'F86', 'F87', 'F88',
			'F89', 'F90', 'F91', 'F92', 'F93'
		);
	}

	/**
	 * target criterions
	 *
	 * @return Array
	 */
	public static function targetCriterions()
	{
		$retvals = array();
		$target_level = (int) Model\Setting::fetch('target_level');
		if (empty($target_level)) return $retvals;

		$additional_criterions = Model\Setting::fetch('additional_criterions');
		$non_exist_and_passed_criterions = Model\Setting::fetch('non_exist_and_passed_criterions');
		foreach (Yaml::each('criterions') as $criterion => $v)
		{
			if (
				(
					strlen($v['level']['name']) <= $target_level ||
					in_array($criterion, $additional_criterions)
				) &&
				! in_array($criterion, $non_exist_and_passed_criterions)
			)
			{
				$retvals[] = $criterion;
			}
		}
		return $retvals;
	}
}
