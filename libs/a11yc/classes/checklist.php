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
class Checklist
{
	/**
	 * fetch page from db
	 *
	 * @param   string     $url
	 * @return  bool|array
	 */
	public static function fetch_page($url)
	{
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = '.Db::escape($url).';';
		return Db::fetch($sql);
	}

	/**
	 * validate page
	 *
	 * @param   string     $url
	 * @return  array
	 */
	public static function validate_page($url)
	{
		$content = Util::fetch_html($url);
		$all_errs = array();

		$codes = array(
			'is_exist_alt_attr_of_img',
			'is_not_empty_alt_attr_of_img_inside_a',
			'is_not_here_link',
			'is_img_input_has_alt',
			'is_are_has_alt',
			'suspicious_elements',
			'appropriate_heading_descending',
		);

		foreach ($codes as $code)
		{
			$errs = Validate::$code($content);
			if (is_array($errs))
			{
				foreach ($errs as $err)
				{
					$all_errs[] = static::message($code, $err);
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
		if ($_POST)
		{
			$esc_url = Db::escape($url);
			$cs = Db::escape($_POST['chk']);

			// delete all
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.$esc_url.';';
			Db::execute($sql);

			// insert
			foreach ($cs as $code => $v)
			{
				// if ( ! isset($v['on']) && empty($v['memo'])) continue;
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`) VALUES ';
				$sql.= '('.$esc_url.', '.Db::escape($code).', '.$v['uid'].', '.$v['memo'].');';
				Db::execute($sql);
			}

			// leveling
			list($results, $checked, $passed_flat) = Evaluate::evaluate_url($url);
			$result = Evaluate::check_result($passed_flat);

			// update/create page
			$done = Db::escape(isset($_POST['done']));
			$date = Db::escape(date('Y-m-d'));
			$standard = intval($_POST['standard']);

			if (static::fetch_page($url))
			{
				$sql = 'UPDATE '.A11YC_TABLE_PAGES.' SET ';
				$sql.= '`date` = '.$date.', `level` = '.$result.', `done` = '.$done.', `standard` = '.$standard;
				$sql.= ' WHERE `url` = '.$esc_url.';';
			}
			else
			{
				$sql = 'INSERT INTO '.A11YC_TABLE_PAGES.' (`url`, `date`, `level`, `done`, `standard`)';
				$sql.= ' VALUES ('.$esc_url.', '.$date.', '.$result.', '.$done.', '.$standard.');';
			}
			Db::execute($sql);
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

		// assign
		View::assign('url', $url);
		View::assign('target_title', Util::fetch_page_title($url));
		View::assign('users', $users);
		View::assign('current_user_id', $current_user_id);
		View::assign('yml', Yaml::fetch(), false);
		View::assign('standards', Yaml::each('standards'));
		$setup = Setup::fetch_setup();
		View::assign('setup', $setup);
		View::assign('checklist_behaviour', intval(@$setup['checklist_behaviour']));
		View::assign('target_level', intval(@$setup['target_level']));
		View::assign('page', static::fetch_page($url));
		View::assign('errs', static::validate_page($url));

		// cs
		if ($url == 'bulk')
		{
			$cs = Bulk::fetch_results();
			View::assign('cs', array());
			View::assign('bulk', $cs);
		}
		else
		{
			$cs = Evaluate::fetch_results($url);
			View::assign('cs', $cs);
			View::assign('bulk', array());
		}

		// form
		View::assign('form', View::fetch_tpl('checklist/form.php'), false);
	}

	/**
	 * results html
	 *
	 * @param   string     $url
	 * @param   array      $users
	 * @return  string
	 */
	public static function results($url, $users = array())
	{
		// is done?
		$page = static::fetch_page($url);
		if ( ! $page['done'])
		{
//			die();
		}

		// base informations
		$setup = Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);

		// evaluate
		list($results, $checked, $passed_flat) = Evaluate::evaluate_url($url);
		$result = Evaluate::check_result($passed_flat);

		$html = '';
		$html.= '<section>';
		$html.= '<h1>'.A11YC_LANG_CHECKLIST_TITLE.'</h1>';
		$html.= '<p>'.A11YC_LANG_TARGET_LEVEL.': '.Util::num2str($target_level).'</p>';
		$html.= '<p>'.A11YC_LANG_CHECKLIST_ACHIEVEMENT.': '.Evaluate::result_str($result, $target_level).'</p>';

		// target level
		$html.= static::part_result($results, $target_level);

		// Additional Criterion
		if ($target_level != 3)
		{
			$html.= '<h2>'.A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL.'</h2>';
			$html.= static::part_result($results, $target_level, false);
		}
		$html.= '</section>';
		return $html;
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
	public static function message($code_str, $place = '')
	{
		$yml = Yaml::fetch();
		if (isset($yml['errors'][$code_str]))
		{
			$ret = $yml['errors'][$code_str]['message'];
			$criterion = $yml['errors'][$code_str]['criterion'];
			$code = $yml['errors'][$code_str]['code'];
			$criterion = $yml['checks'][$criterion][$code]['criterion'];
			$ret.= ' (<a href="'.$criterion['url'].'"'.A11YC_TARGET.' title="'.$criterion['name'].'">'.Util::key2code($criterion['code']).'</a>, ';
			$ret.= '<a href="'.A11YC_DOC_URL.$code.'&amp;criterion='.$yml['errors'][$code_str]['criterion'].'"'.A11YC_TARGET.'>Doc</a>)';
			$ret.= $place !== '' ? ': <strong>'.$place.'</strong>' : '';
			return $ret;
		}
		return FALSE;
	}
}
