<?php
/**
 * A11yc\Controller\Checklist
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

use A11yc\Model;
use A11yc\Validate;

class Checklist
{
	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function actionCheck()
	{
//		$url = Util::enuniqueUri(Input::param('url', '', FILTER_VALIDATE_URL));
		$url = Util::enuniqueUri(Input::param('url', ''));
		static::check($url);
	}

	/**
	 * check
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function check($url)
	{
		// url
		if ( ! $url) Util::error('Invalid Access');

		// page existence
		if ($url != 'bulk' && ! Crawl::isPageExist($url))
		{
			Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);
		}

		// users
		$users = Users::fetchUsersOpt();
		$current_user = Users::fetchCurrentUser();

		// title and page
		$page = array();
		if ($url == 'bulk')
		{
			$title = A11YC_LANG_BULK_TITLE;
		}
		else
		{
			$title = A11YC_LANG_CHECKLIST_TITLE.':'.Model\Html::fetchPageTitle($url);
			$page = Model\Pages::fetchPage($url);
		}

		// assign
		View::assign('current_user', $current_user);
		View::assign('users',        $users);
		View::assign('url',          $url);
		View::assign('alt_url',      Arr::get($page, 'alt_url'));
		View::assign('title',        $title);

		// core form
		self::form($url, $users, Arr::get($current_user, 'id'));

		View::assign('body', View::fetchTpl('checklist/checklist.php'), FALSE);
	}

	/**
	 * dbio
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function dbio($url)
	{
		Model\Checklist::update($url, Input::postArr('chk'));
		Model\Results::update($url, Input::postArr('results'));

		// update page
		$page_title       = stripslashes(Input::post('page_title'));
		$done             = intval(Input::post('done', 0));
		$done_date        = Input::post('done_date') ?
											date('Y-m-d', strtotime(Input::post('done_date'))) :
											date('Y-m-d');
		$alt_url          = Util::urldec(Input::post('alt_url'));
		$standard         = intval(Input::post('standard'));
		$selection_reason = intval(Input::post('selection_reason'));
		$type             = substr($url, -4) == '.pdf' ? 2 : 1; // 1: html

		Model\Pages::updateField($url, 'title', $page_title);
		Model\Pages::updateField($url, 'type', $type);
		Model\Pages::updateField($url, 'done', $done);
		Model\Pages::updateField($url, 'date', $done_date);
		Model\Pages::updateField($url, 'updated_at', date('Y-m-d H:i:s'));
		Model\Pages::updateField($url, 'alt_url', $alt_url);
		Model\Pages::updateField($url, 'standard', $standard);
		Model\Pages::updateField($url, 'selection_reason', $selection_reason);
		Model\Pages::updateField($url, 'level', Evaluate::getLevelByUrl($url));

		// message
		Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
	}

	/**
	 * form
	 * need to wrap <form> and add a submit button
	 * this method also used by bulk
	 *
	 * @param  String $url
	 * @param  Array $users
	 * @param  Integer $current_user_id
	 * @return Void
	 */
	public static function form($url, $users = array(), $current_user_id = null)
	{
		// dbio
		if (Input::isPostExists())
		{
			static::dbio($url);
		}

		// is bulk
		$is_bulk = ($url == 'bulk');

		// page
		$page = array();
		if ( ! $is_bulk && $url)
		{
			$page = self::getPage($url);

			// automatic check
			Validate::$do_link_check = Input::post('do_link_check', false);
			Validate::$do_css_check  = Input::post('do_css_check', false);
			Validate::url($url);
		}

		// get basic values
		list($settings, $standards, $standard, $done_date, $issues) = self::getBasicValues($url, $page);

		// is new
		$is_new = is_array($page) && Arr::get($page, 'updated_at') !== NULL ? FALSE : TRUE;

		// reference url
		$refs = Values::getRefUrls();

		// assign
		View::assign('target_title', Model\Html::fetchPageTitle($url));
		View::assign('url', $url);
		View::assign('statuses', Values::issueStatus());
		View::assign('machine_check_status', Values::machineCheckStatus());
		View::assign('issues', $issues);
		View::assign('selection_reasons', Values::filteredSelectionReasons());
		View::assign('refs', $refs[$standard]);
		View::assign('users', $users);
		View::assign('current_user_id', $current_user_id);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('standards', $standards);
		View::assign('settings', $settings);
		View::assign('done_date', $done_date);
		View::assign('target_level', intval(@$settings['target_level']));
		View::assign('page', $page);
		View::assign('additional_criterions', join('","',Values::additionalCriterions()));
		View::assign('is_new', $is_new);
		View::assign('is_bulk', $is_bulk);
		View::assign('checkstatus', self::getCheckStatus($url));

		self::assginValidation($url, $is_new, $is_bulk);

		// form
		View::assign('form', View::fetchTpl('checklist/form.php'), FALSE);
	}

	/**
	 * get basic values
	 *
	 * @param  String $url
	 * @return Array
	 */
	private static function getCheckStatus($url)
	{
		$retvals = array('passed' => array(), 'failed' => array());
		foreach (Validate\Get::logs($url) as $v)
		{
			foreach ($v as $vv)
			{
				if ($vv == -1)
				{
				}
				else if ($vv == 2)
				{
				}
			}
		}

		return $retvals;
	}

	/**
	 * get basic values
	 *
	 * @param  String $url
	 * @param  Array|Bool $page
	 * @return Array
	 */
	private static function getBasicValues($url, $page)
	{
		if ( ! is_array($page)) Util::error('invalid value was given');

		// settings
		$settings = Model\Settings::fetchAll();

		// standards
		$standards = Yaml::each('standards');
		$standard = Arr::get($settings, 'standard', 0);
		$standards = array(Arr::get($standards, $standard, array()));

		// done
		$done_date = is_array($page) ? Arr::get($page, 'date') : 0;
		$done_date = $done_date == '0' ? '' : $done_date;
		if ($done_date)
		{
			$done_date = date('Y-m-d', strtotime($done_date));
		}

		// issues
		$yml = Yaml::fetch();
		$issues = array();
		foreach (array_keys($yml['criterions']) as $criterion)
		{
			$issues[$criterion] = Model\Issues::fetch4Checklist($url, $criterion);
		}

		return array($settings, $standards, $standard, $done_date, $issues);
	}

	/**
	 * get page
	 *
	 * @param  String $url
	 * @return Array
	 */
	private static function getPage($url)
	{
		$page = Model\Pages::fetchPage($url, true);
		if ( ! $page && $url)
		{
			Model\Pages::addPage($url);
			$force = true;
			$page = Model\Pages::fetchPage($url, $force);
			if ( ! $page)
			{
				Session::add(
					'messages',
					'errors',
					A11YC_LANG_ERROR_COULD_NOT_GET_HTML.': '. Util::s($url));
			}
		}

		return $page;
	}

	/**
	 * assgin vaidated value
	 *
	 * @param  String $url
	 * @param  Bool $is_new
	 * @param  Bool $is_bulk
	 * @return Void
	 */
	private static function assginValidation($url, $is_new, $is_bulk)
	{
		if ($is_bulk || $is_new)
		{
			View::assign('results', Model\Bulk::fetchResults());
			View::assign('cs', Model\Bulk::fetchChecks());
		}
		else
		{
			View::assign('results', Model\Results::fetch($url));
			View::assign('cs', Model\Checklist::fetch($url));
		}

		$errs = Validate\Get::errors($url) ?: array('errors' => array(), 'notices' => array());

		// validation
//		View::assign('is_call_from_post', true);
		View::assign('errs_cnts', Validate\Get::errorCnts($url));
		View::assign('errs', $errs, FALSE);
		View::assign('logs', Validate\Get::logs($url) ?: array());
		View::assign('raw', nl2br(Validate\Get::highLightedHtml($url)), FALSE);
		View::assign('validation_result', View::fetchTpl('checklist/validate.php'), FALSE);
	}
}
