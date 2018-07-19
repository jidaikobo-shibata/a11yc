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
		switch (Arr::get(Model\Settings::fetchAll(), 'selected_method'))
		{
			case 0: // not site unit
				$selection_reasons = array($selection_reasons[6]);
				break;
			case 1: // all
				$selection_reasons = array($selection_reasons[3]);
				break;
			case 2: // random
				$selection_reasons = array($selection_reasons[2]);
				break;
			case 3: // representative
				$selection_reasons = array($selection_reasons[1]);
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
	 * get additional criterions
	 *
	 * @return Array
	 */
	public static function additionalCriterions()
	{
		$settings = Model\Settings::fetchAll();
		if(isset($settings['additional_criterions']) && $settings['additional_criterions'])
		{
			$str = str_replace('&quot;', '"', $settings['additional_criterions']);
			return unserialize($str);
		}
		return array();
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
			A11YC_LANG_ISSUES_STATUS_1,
			A11YC_LANG_ISSUES_STATUS_2,
			A11YC_LANG_ISSUES_STATUS_3,
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
	 * standaoneConsts
	 *
	 * @return Array
	 */
	public static function standaoneConsts()
	{
		// max post a day by ip
		defined('A11YC_POST_IP_MAX_A_DAY') or define('A11YC_POST_IP_MAX_A_DAY', 150);
		defined('A11YC_POST_COOKIE_A_10MIN') or define('A11YC_POST_COOKIE_A_10MIN', 10);

		// script name
		defined('A11YC_POST_SCRIPT_NAME') or define('A11YC_POST_SCRIPT_NAME', '/post.php');

		// Google Analytics
		defined('A11YC_POST_GOOGLE_ANALYTICS_CODE') or define('A11YC_POST_GOOGLE_ANALYTICS_CODE', '');
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
		);
	}
}
