<?php
/**
 * A11yc\Checklist
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
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
		if ( ! $setup['target_level'])
		{
			Session::add('messages', 'errors', array(A11YC_LANG_ERROR_NON_TARGET_LEVEL));
		}

		$url = isset($_GET['url']) ? Util::urldec($_GET['url']) : '';
		$url = empty($url) && isset($_POST['url']) ? Util::urldec($_POST['url']) : $url;

		static::checklist($url);
	}

	/**
	 * fetch page from db
	 *
	 * @param   string     $url
	 * @return  bool|array
	 */
	public static function fetch_page($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?;';
		return Db::fetch($sql, array($url));
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
			'empty_alt_attr_of_img_inside_a',
			'here_link',
			'tell_user_file_type',
			'same_urls_should_have_same_text',
			'form_and_labels',

			// single tags
			'check_doctype',
			'alt_attr_of_img',
			'img_input_has_alt',
			'area_has_alt',
			'suspicious_elements',
			'appropriate_heading_descending',
			'same_alt_and_filename_of_img',
			'meanless_element',
			'style_for_structure',
			'invalid_tag',
			'titleless_frame',
			'meta_refresh',
			'titleless',
			'langless',

			// non tag
			'suspicious_attributes',
			'duplicated_ids_and_accesskey',
			'ja_word_breaking_space',
			'same_page_title_in_same_site',
		);

		if ($link_check)
		{
			$codes[] = 'link_check';
		}

		foreach ($codes as $code)
		{
			Validate_Validation::$code();
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
	 * dbio
	 *
	 * @param   string     $url
	 * @return  void
	 */
	public static function dbio($url)
	{
		$url = Util::urldec($url);

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

		if ($_POST)
		{
			// delete all from checks
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = ?;';
			Db::execute($sql, array($url));

			// insert checks
			foreach ($_POST['chk'] as $code => $v)
			{
				// if ( ! isset($v['on']) && empty($v['memo'])) continue;
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`)';
				$sql.= ' VALUES (?, ?, ?, ?);';
				Db::execute($sql, array($url, $code, $v['uid'], $v['memo']));
			}

			// leveling page
			list($results, $checked, $passed_flat) = Evaluate::evaluate_url($url);
			$result = Evaluate::check_result($passed_flat);

			// update/create page
			$done = isset($_POST['done']) ? 1 : 0;
			$date = date('Y-m-d');
			$page_title = $_POST['page_title'];
			$standard = intval($_POST['standard']);
			$selection_reason = intval($_POST['selection_reason']);
			$r = false;

			if (static::fetch_page($url))
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
				$sql.= '`date` = ?, `level` = ?, `done` = ?, `standard` = ?, `page_title` =?, `selection_reason` =?';
				$sql.= ' WHERE `url` = ?;';
				$r = Db::execute($sql, array($date, $result, $done, $standard, $page_title, $selection_reason, $url));
			}
			else
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_PAGES;
				$sql.= ' (`url`, `date`, `level`, `done`, `standard`, `trash`, `page_title`, `add_date`, `selection_reason`)';
				$sql.= ' VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?);';
				$r = Db::execute($sql, array($url, $date, $result, $done, $standard, $page_title, date('Y-m-d H:i:s'), $selection_reason));
			}

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
	 * checklist html
	 *
	 * @return  string
	 */
	public static function checklist($url)
	{
		// url
		if ( ! $url) die('Invalid Access');

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
		$selection_reasons = array(
			A11YC_LANG_CANDIDATES_ALL,
			A11YC_LANG_CANDIDATES_IMPORTANT,
			A11YC_LANG_CANDIDATES_RANDOM,
			A11YC_LANG_CANDIDATES_PAGEVIEW,
			A11YC_LANG_CANDIDATES_NEW,
			A11YC_LANG_CANDIDATES_ETC,
		);

		// assign
		View::assign('selection_reasons', $selection_reasons);
		View::assign('url', $url);
		View::assign('target_title', Util::fetch_page_title($url));
		View::assign('users', $users);
		View::assign('current_user_id', $current_user_id);
		View::assign('yml', Yaml::fetch(), false);
		View::assign('standards', Yaml::each('standards'));
		$setup = Controller_Setup::fetch_setup();
		View::assign('setup', $setup);
		View::assign('checklist_behaviour', intval(@$setup['checklist_behaviour']));
		View::assign('target_level', intval(@$setup['target_level']));
		View::assign('page', static::fetch_page($url));
		View::assign('link_check', isset($_POST['do_link_check']));

		// cs
		$bulk = Controller_Bulk::fetch_results();
		$cs = Evaluate::fetch_results($url);
		View::assign('bulk', $bulk);
		View::assign('cs', $url == 'bulk' ? array() : $cs);

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
			$ret.= '<a href="'.$criterion['url'].'"'.A11YC_TARGET.' title="'.$criterion['name'].'">'.Util::key2code($criterion['code']).'</a>)</dt>';

			// dd
			$ret.= '<dd class="a11yc_validation_error_str a11yc_level_'.$lv.'" data-level="'.$level.'" data-place="'.Util::s($place['id']).'">'.Util::s($place['str']).'</dd>';
			$ret.= '<dd class="a11yc_validation_error_link a11yc_level_'.$lv.'"><a href="#'.$anchor .'" class="a11yc_hasicon">Code</a></dd>';
			return $ret;
		}
		return FALSE;
	}
}
