<?php
/**
 * A11yc\Controller_Checklist
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Controller_Checklist
{
	static public $err_cnts = array('a' => 0, 'aa' => 0, 'aaa' => 0);

	/**
	 * action index
	 *
	 * @return Void
	 */
	public static function Action_Index()
	{
		$setup = Controller_Setup::fetch_setup();
		if ( ! isset($setup['target_level']))
		{
			Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
		}

		$get_url = Input::get('url', '', FILTER_VALIDATE_URL);
		$post_url = Input::post('url', '', FILTER_VALIDATE_URL);
		$url = ! empty($get_url) && is_string($get_url) ? Util::urldec($get_url) : '';
		$url = empty($url) && ! empty($post_url) && is_string($post_url) ? Util::urldec($post_url) : $url;

		static::checklist($url);
	}

	/**
	 * validate page
	 *
	 * @param  String $url
	 * @param  Bool $link_check
	 * @return Array
	 */
	public static function validate_page($url, $link_check = FALSE)
	{
		$content = Crawl::fetch_html($url);
		if ( ! $content) return array();
		$all_errs = array(
			'notices' => array(),
			'errors' => array()
		);
		Validate::set_html($content);
		Crawl::set_target_path($url); // for same_urls_should_have_same_text
		$codes = Validate::$codes;
		$yml = Yaml::fetch();

		// link check
		if ( ! $link_check)
		{
			unset($codes['link_check']);
		}

		// validate
		foreach ($codes as $method => $class)
		{
			$class::$method();
		}

		// errors
		if (Validate::get_error_ids())
		{
			foreach (Validate::get_error_ids() as $code => $errs)
			{
				foreach ($errs as $key => $err)
				{
					$err_type = isset($yml['errors'][$code]['notice']) ? 'notices' : 'errors';
					$all_errs[$err_type][] = static::message($code, $err, $key);
				}
			}
		}

		return $all_errs;
	}

	/**
	 * update_page_level
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function update_page_level($url)
	{
		$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `level` = ? WHERE `url` = ?'.Controller_Setup::curent_version_sql().';';
		Db::execute($sql, array(Evaluate::check_level_url($url), $url));
	}

	/**
	 * dbio
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function dbio($url)
	{
		$url = Util::urldec($url);

		if (Input::is_post_exists())
		{
			// NGs
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS_NGS.' WHERE `url` = ?';
			$sql.= Controller_Setup::curent_version_sql().';';
			Db::execute($sql, array($url));

			$post_ngs = Input::post_arr('ngs');
			foreach ($post_ngs as $criterion => $v)
			{
				if ( ! trim($v['memo'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS_NGS;
				$sql.= ' (`url`, `criterion`, `uid`, `memo`, `version`)';
				$sql.= ' VALUES (?, ?, ?, ?, "");';
				$memo = stripslashes($v['memo']);
				Db::execute($sql, array($url, $criterion, (int) $v['uid'], $memo));
			}

			// delete all from checks
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?';
			$sql.= Controller_Setup::curent_version_sql().';';
			Db::execute($sql, array($url));

			// insert checks
			$post_chk = Input::post_arr('chk');
			foreach ($post_chk as $code => $v)
			{
				if ( ! isset($v['on']) && empty($v['memo'])) continue;
				$passed = isset($v['on']);
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS;
				$sql.= ' (`url`, `code`, `uid`, `memo`, `passed`, `version`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, 0);';
				$memo = stripslashes($v['memo']);
				Db::execute($sql, array($url, $code, (int) $v['uid'], $memo, (int) $passed));
			}

			// update/create page
			$done = Input::post('done') ? 1 : 0;
			$date = Input::post('done_date', date('Y-m-d'));
			$date = date('Y-m-d', strtotime($date));
			$page_title = stripslashes(Input::post('page_title'));
			$alt_url = Util::urldec(Input::post('alt_url'));
			$standard = intval(Input::post('standard'));
			$selection_reason = intval(Input::post('selection_reason'));
			$r = FALSE;
			if (Controller_Pages::fetch_page($url))
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
				$sql.= '`date` = ?, `done` = ?, `standard` = ?,';
				$sql.= ' `page_title` = ?, `selection_reason` = ?, `alt_url` = ?';
				$sql.= ' WHERE `url` = ?'.Controller_Setup::curent_version_sql().';';
				$r = Db::execute(
					$sql,
					array($date, $done, $standard, $page_title, $selection_reason, $alt_url, $url)
				);
			}
			else
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
				$sql.= ' (`url`, `date`, `done`, `standard`, `trash`,';
				$sql.= ' `page_title`, `add_date`, `selection_reason`, `alt_url`, `version`)';
				$sql.= ' VALUES (?, ?, ?, ?, 0, ?, ?, ?, 0);';
				$r = Db::execute(
					$sql,
					array($url, $date, $done, $standard, $page_title, date('Y-m-d H:i:s'), $selection_reason, $alt_url)
				);
			}

			// update page level
			static::update_page_level($url);

			// message
			if ($r)
			{
				Session::add('messages', 'messages', A11YC_LANG_UPDATE_SUCCEED);
			}
			else
			{
				Session::add('messages', 'errors', A11YC_LANG_UPDATE_FAILED);
			}
		}
	}

	/**
	 * checklist
	 *
	 * @param  String $url
	 * @return Void
	 */
	public static function checklist($url)
	{
		// url
		if ( ! $url) Util::error('Invalid Access');

		// page existence
		if ($url != 'bulk' && ! Crawl::is_page_exist($url))
		{
			Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);
			if ( ! headers_sent())
			{
				header('location:'.A11YC_PAGES_URL.'&list=list&no_url='.Util::urlenc($url));
				exit();
			}
		}

		// change url?
		// $url = static::change_url($url);

		// users
		$users = array();
		foreach (Users::fetch_users() as $k => $v)
		{
			$users[$k] = Util::s($v[0]);
		}

		// current user
		$current_user = Users::fetch_current_user();

		// title
		if ($url == 'bulk')
		{
			$title = A11YC_LANG_BULK_TITLE;
		}
		else
		{
			$title = A11YC_LANG_CHECKLIST_TITLE.':'.Util::fetch_page_title($url);
		}

		// page
		$page = Controller_Pages::fetch_page($url);

		// assign
		View::assign('current_user', $current_user);
		View::assign('users', $users);
		View::assign('url', $url);
		View::assign('alt_url', $page['alt_url']);
		View::assign('title', $title);

		static::form($url, $users, Arr::get($current_user, 'id'));
		View::assign('body', View::fetch_tpl('checklist/checklist.php'), FALSE);
	}

	/**
	 * change url
	 * suspicious activation. so, pending.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function change_url($url)
	{
		// post only
		$mod_url = Input::post('mod_url', '', FILTER_VALIDATE_URL);
		if ( ! $mod_url) return $url;

		// no cahnge
		$old_url = $url;
		if ($old_url == $mod_url) return $url;

		// is exists?
		$sql = 'SELECT `url` FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?';
		$sql.= Controller_Setup::curent_version_sql().';';
		$results = Db::fetch_all($sql, array($old_url));

		// no record to change
		if (empty($results)) return $url;

		// change url
		$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `url` = %s WHERE `url` = %s';
		$sql.= Controller_Setup::curent_version_sql().';';
		$result = Db::fetch_all($sql, array($mod_url, $old_url));

		// change checks
		$sql = 'UPDATE '.A11YC_TABLE_CHECKS_NGS.' SET `url` = %s WHERE `url` = %s';
		$sql.= Controller_Setup::curent_version_sql().';';
		$result = Db::fetch_all($sql, array($mod_url, $old_url));

		$sql = 'UPDATE '.A11YC_TABLE_CHECKS.' SET `url` = %s WHERE `url` = %s';
		$sql.= Controller_Setup::curent_version_sql().';';
		$result = Db::fetch_all($sql, array($mod_url, $old_url));

		return $mod_url;
	}

	/**
	 * get selection reasons
	 *
	 * @return Array
	 */
	public static function selection_reasons()
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
	 * form html
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
		static::dbio($url);

		// vals
		$page = Controller_Pages::fetch_page($url);
		$setup = Controller_Setup::fetch_setup();

		// selection reason
		$selection_reasons = static::selection_reasons();
		$selected_method = Arr::get($setup, 'selected_method');
		switch ($selected_method)
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

		// standards
		$standards = Yaml::each('standards');
		$standard = Arr::get($setup, 'standard');
		if (is_null($standard))
		{
			Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
			return;
		}
		$standards = array($standards['standards'][$standard]);

		// done
		$done_date = is_array($page) ? Arr::get($page, 'date') : 0;
		$done_date = $done_date == '0' ? '' : $done_date;
		if ($done_date)
		{
			$done_date = date('Y-m-d', strtotime($done_date));
		}

		// assign
		View::assign('selection_reasons', $selection_reasons);
		View::assign('url', $url);
		View::assign('target_title', Util::fetch_page_title($url));
		View::assign('users', $users);
		View::assign('current_user_id', $current_user_id);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('standards', $standards);
		View::assign('setup', $setup);
		View::assign('done_date', $done_date);
		View::assign('checklist_behaviour', intval(@$setup['checklist_behaviour']));
		View::assign('target_level', intval(@$setup['target_level']));
		View::assign('page', $page);
		View::assign('link_check', Input::post('do_link_check', FALSE));
		View::assign('additional_criterions', join('","',Controller_Setup::additional_criterions()));

		// cs
		$bulk = Controller_Bulk::fetch_results();
		$cs = Evaluate::fetch_results($url);
		View::assign('bulk', $bulk);
		View::assign('cs', $url == 'bulk' ? array() : $cs);

		// ngs
		$bulk_ngs = Controller_Bulk::fetch_ngs();
		$ngs = Evaluate::fetch_ngs($url);
		View::assign('bulk_ngs', $bulk_ngs);
		View::assign('cs_ngs', $url == 'bulk' ? array() : $ngs);

		// is new
		View::assign('is_new', is_array($page) && Arr::get($page, 'level') !== NULL ? FALSE : TRUE);

		// validation
		View::assign('ajax_validation', View::fetch_tpl('checklist/ajax.php'), FALSE);

		// form
		View::assign('form', View::fetch_tpl('checklist/form.php'), FALSE);
	}

	/**
	 * part of result html
	 *
	 * @param  Array $results
	 * @param  Integer $target_level
	 * @param  Bool $include
	 * @return Void
	 */
	public static function part_result($results, $target_level, $include = TRUE)
	{
		View::assign('is_total', ! Input::get('url'));
		View::assign('setup', Controller_Setup::fetch_setup());
		View::assign('results', $results);
		View::assign('target_level', $target_level);
		View::assign('include', $include);
		View::assign('yml', Yaml::fetch(), FALSE);
		View::assign('result', View::fetch_tpl('checklist/part_result.php'), FALSE);
	}

	/**
	 * message
	 *
	 * @param String $code_str
	 * @param Array $place
	 * @param String $key
	 * @param String $docpath
	 * @return String|Bool
	 */
	public static function message($code_str, $place, $key, $docpath = '')
	{
		$yml = Yaml::fetch();
		if (isset($yml['errors'][$code_str]))
		{
			$docpath = $docpath ?: A11YC_DOC_URL;

			$anchor = $code_str.'_'.$key;

			// level
			$lv = strtolower($yml['criterions'][$yml['errors'][$code_str]['criterion']]['level']['name']);

			// count errors
			if ( ! isset($yml['errors'][$code_str]['notice'])) static::$err_cnts[$lv]++;

			// dt
			$ret = '<dt id="index_'.$anchor.'" tabindex="-1" class="a11yc_level_'.$lv.'">'.$yml['errors'][$code_str]['message'];

			// dt - information
			$criterion_code = $yml['errors'][$code_str]['criterion'];
			$code = $yml['errors'][$code_str]['code'];
			$level = $yml['checks'][$criterion_code][$code]['criterion']['level']['name'];
			$criterion = $yml['checks'][$criterion_code][$code]['criterion'];

			$ret.= ' ('.strtoupper($lv).' ';
			$ret.= '<a href="'.$docpath.$code.'&amp;criterion='.$yml['errors'][$code_str]['criterion'].'"'.A11YC_TARGET.'>Doc</a> ';
			$ret.= '<a href="'.$criterion['url'].'"'.A11YC_TARGET.' title="'.$criterion['name'].'">'.Util::key2code($criterion['code']).'</a>)';
			$ret.= '<a href="#'.$anchor .'" class="a11yc_validation_error_link a11yc_level_'.$lv.' a11yc_hasicon"><span class="a11yc_icon_fa a11yc_icon_arrow_b" role="presentation" aria-hidden="true"></span>Code</a></dt>';

			// dd
			$ret.= '<dd class="a11yc_validation_error_str a11yc_level_'.$lv.'" data-level="'.$level.'" data-place="'.Util::s($place['id']).'">'.Util::s($place['str']).'</dd>';
//			$ret.= '<dd class="a11yc_validation_error_link a11yc_level_'.$lv.'"><a href="#'.$anchor .'" class="a11yc_hasicon">Code</a></dd>';
			return $ret;
		}
		return FALSE;
	}
}
