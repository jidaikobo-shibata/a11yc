<?php
/**
 * A11yc\Checklist
 *
 * @package    part of A11yc
 * @version    1.0
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
	 * @return  void
	 */
	public static function Action_Index()
	{
		$setup = Controller_Setup::fetch_setup();
		if ( ! isset($setup['target_level']))
		{
			Session::add('messages', 'errors', A11YC_LANG_ERROR_NON_TARGET_LEVEL);
		}

		$url = Input::get('url') ? Util::urldec(Input::get('url')) : '';
		$url = empty($url) && Input::post('url') ? Util::urldec(Input::post('url')) : $url;

		static::checklist($url);
	}

	/**
	 * validate page
	 *
	 * @param   string     $url
	 * @return  array
	 */
	public static function validate_page($url, $link_check = false)
	{
		$content = Util::fetch_html($url);
		if ( ! $content) return array();
		$all_errs = array();
		Validate::set_target_path($url);
		Validate::set_html($content);

		$codes = array(
			// elements
			array('\A11yc\Validate_Alt', 'empty_alt_attr_of_img_inside_a'),
			array('\A11yc\Validate_Link', 'here_link'),
			array('\A11yc\Validate_Link', 'tell_user_file_type'),
			array('\A11yc\Validate_Link', 'same_urls_should_have_same_text'),
			array('\A11yc\Validate_Form', 'form_and_labels'),

			// single tag
			array('\A11yc\Validate_Alt', 'alt_attr_of_img'),
			array('\A11yc\Validate_Alt', 'img_input_has_alt'),
			array('\A11yc\Validate_Alt', 'area_has_alt'),
			array('\A11yc\Validate_Alt', 'same_alt_and_filename_of_img'),
			array('\A11yc\Validate_Validation', 'suspicious_elements'),
			array('\A11yc\Validate_Validation', 'appropriate_heading_descending'),
			array('\A11yc\Validate_Validation', 'meanless_element'),
			array('\A11yc\Validate_Validation', 'style_for_structure'),
			array('\A11yc\Validate_Validation', 'invalid_tag'),
			array('\A11yc\Validate_Validation', 'titleless_frame'),
			array('\A11yc\Validate_Head', 'check_doctype'),
			array('\A11yc\Validate_Head', 'meta_refresh'),
			array('\A11yc\Validate_Head', 'titleless'),
			array('\A11yc\Validate_Head', 'langless'),
			array('\A11yc\Validate_Head', 'viewport'),
		);

		// single tag
		if ($link_check)
		{
			$codes[] = array('\A11yc\Validate_Link', 'link_check');
		}

		// non tag
		$codes[] = array('\A11yc\Validate_Validation', 'suspicious_attributes');
		$codes[] = array('\A11yc\Validate_Validation', 'duplicated_ids_and_accesskey');
		$codes[] = array('\A11yc\Validate_Validation', 'ja_word_breaking_space');
		$codes[] = array('\A11yc\Validate_Head', 'same_page_title_in_same_site');

		foreach ($codes as $code)
		{
			$c = $code[0];
			$m = $code[1];
			$c::$m();
		}

		if (Validate::get_error_ids())
		{
			foreach (Validate::get_error_ids() as $code => $errs)
			{
				foreach ($errs as $key => $err)
				{
					$all_errs[] = static::message($code, $err, $key);
				}
			}
		}
		return $all_errs;
	}

	/**
	 * update_page_level
	 *
	 * @param   string     $url
	 * @return  void
	 */
	public static function update_page_level($url)
	{
		$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET `level` = ? WHERE `url` = ?;';
		Db::execute($sql, array(Evaluate::check_level_url($url), $url));
	}

	/**
	 * dbio
	 *
	 * @param   string     $url
	 * @return  void
	 */
	public static function dbio($url)
	{
		$url = Util::urldec($url);

		if (Input::post())
		{
			// NGs
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS_NGS.' WHERE `url` = ?;';
			Db::execute($sql, array($url));

			foreach (Input::post('ngs') as $criterion => $v)
			{
				if ( ! trim($v['memo'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS_NGS.' (`url`, `criterion`, `uid`, `memo`)';
				$sql.= ' VALUES (?, ?, ?, ?);';
				Db::execute($sql, array($url, $criterion, $v['uid'], $v['memo']));
			}

			// delete all from checks
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?;';
			Db::execute($sql, array($url));

			// insert checks
			foreach (Input::post('chk') as $code => $v)
			{
				if ( ! isset($v['on']) && empty($v['memo'])) continue;
				$passed = isset($v['on']);
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`, `passed`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?);';
				Db::execute($sql, array($url, $code, $v['uid'], $v['memo'], $passed));
			}

			// update/create page
			$done = Input::post('done') ? 1 : 0;
			$date = date('Y-m-d');
			$page_title = Input::post('page_title');
			$standard = intval(Input::post('standard'));
			$selection_reason = intval(Input::post('selection_reason'));
			$r = false;
			if (Controller_Pages::fetch_page($url))
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
				$sql.= '`date` = ?, `done` = ?, `standard` = ?, `page_title` = ?, `selection_reason` = ?';
				$sql.= ' WHERE `url` = ?;';
				$r = Db::execute($sql, array($date, $done, $standard, $page_title, $selection_reason, $url));
			}
			else
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
				$sql.= ' (`url`, `date`, `done`, `standard`, `trash`, `page_title`, `add_date`, `selection_reason`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?);';
				$r = Db::execute($sql, array($url, $date, $done, $standard, $page_title, date('Y-m-d H:i:s'), $selection_reason));
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
	 * @return  string
	 */
	public static function checklist($url)
	{
		// url
		if ( ! $url) Util::error('Invalid Access');

		// page existence
		if ($url != 'bulk' && ! Util::is_page_exist($url))
		{
			Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);
			if ( ! headers_sent())
			{
				header('location:'.A11YC_PAGES_URL.'&list=list&no_url='.Util::urlenc($url));
				exit();
			}
		}

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

		// assign
		View::assign('current_user', $current_user);
		View::assign('users', $users);
		View::assign('url', $url);
		View::assign('title', $title);

		static::form($url, $users, $current_user['id']);
		View::assign('body', View::fetch_tpl('checklist/checklist.php'), false);
	}

	/**
	 * get selection reasons
	 *
	 * @return  array()
	 */
	public static function selection_reasons()
	{
		return array(
			'-',
			A11YC_LANG_CANDIDATES_IMPORTANT,
			A11YC_LANG_CANDIDATES_RANDOM,
			A11YC_LANG_CANDIDATES_ALL,
			A11YC_LANG_CANDIDATES_PAGEVIEW,
			A11YC_LANG_CANDIDATES_NEW,
			A11YC_LANG_CANDIDATES_ETC,
		);
	}

	/**
	 * form html
	 * need to wrap <form> and add a submit button
	 * this method also used by bulk
	 *
	 * @param   string     $url
	 * @param   array      $users
	 * @param   integer    $current_user_id
	 * @return  string
	 */
	public static function form($url, $users = array(), $current_user_id = null)
	{
		// dbio
		static::dbio($url);

		// selection reason
		$selection_reasons = static::selection_reasons();

		// assign
		$page = Controller_Pages::fetch_page($url);
		$setup = Controller_Setup::fetch_setup();
		View::assign('selection_reasons', $selection_reasons);
		View::assign('url', $url);
		View::assign('target_title', Util::fetch_page_title($url));
		View::assign('users', $users);
		View::assign('current_user_id', $current_user_id);
		View::assign('yml', Yaml::fetch(), false);
		View::assign('standards', Yaml::each('standards'));
		View::assign('setup', $setup);
		View::assign('checklist_behaviour', intval(@$setup['checklist_behaviour']));
		View::assign('target_level', intval(@$setup['target_level']));
		View::assign('page', $page);
		View::assign('link_check', Input::post('do_link_check', false));

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
		View::assign('is_new', Arr::get($page, 'level') ? false : true);

		// validation
		View::assign('ajax_validation', View::fetch_tpl('checklist/ajax.php'), false);

		// form
		View::assign('form', View::fetch_tpl('checklist/form.php'), false);
	}

	/**
	 * part of result html
	 *
	 * @param   array      $results
	 * @param   integer    $target_level
	 * @param   bool       $include
	 * @return  string
	 */
	public static function part_result($results, $target_level, $include = true)
	{
		View::assign('is_total', ! \A11yc\Input::get('url'));
		View::assign('setup', Controller_Setup::fetch_setup());
		View::assign('results', $results);
		View::assign('target_level', $target_level);
		View::assign('include', $include);
		View::assign('yml', Yaml::fetch(), false);
		View::assign('result', View::fetch_tpl('checklist/part_result.php'), false);
	}

	/**
	 * message
	 *
	 * @return  str
	 */
	public static function message($code_str, $place, $key)
	{
		$yml = Yaml::fetch();
		if (isset($yml['errors'][$code_str]))
		{
			$anchor = $code_str.'_'.$key;

			// level
			$lv = strtolower($yml['criterions'][$yml['errors'][$code_str]['criterion']]['level']['name']);

			// count errors
			static::$err_cnts[$lv]++;

			// dt
			$ret = '<dt id="index_'.$anchor.'" tabindex="-1" class="a11yc_level_'.$lv.'">'.$yml['errors'][$code_str]['message'];

			// dt - information
			$criterion_code = $yml['errors'][$code_str]['criterion'];
			$code = $yml['errors'][$code_str]['code'];
			$level = $yml['checks'][$criterion_code][$code]['criterion']['level']['name'];
			$criterion = $yml['checks'][$criterion_code][$code]['criterion'];
			$ret.= ' ('.strtoupper($lv).' ';
			$ret.= '<a href="'.A11YC_DOC_URL.$code.'&amp;criterion='.$yml['errors'][$code_str]['criterion'].'"'.A11YC_TARGET.'>Doc</a> ';
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
