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
		$sql = 'SELECT * FROM '.A11YC_TABLE_PAGES.' WHERE `url` = '.Db::escapeStr($url).';';
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
		$content = @file_get_contents($url);
		$all_errs = array();

		$codes = array(
			'is_exist_alt_attr_of_img',
			'is_not_empty_alt_attr_of_img_inside_a',
			'is_not_here_link',
			'is_img_input_has_alt',
			'is_are_has_alt',
			'suspicious_elements',
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
			$esc_url = Db::escapeStr($url);
			$cs = Db::escapeStr($_POST['chk']);

			// delete all
			$sql = 'DELETE FROM '.A11YC_TABLE_CHECKS.' WHERE `url` = '.$esc_url.';';
			Db::execute($sql);

			// insert
			foreach ($cs as $code => $v)
			{
				// if ( ! isset($v['on']) && empty($v['memo'])) continue;
				if ( ! isset($v['on'])) continue;
				$sql = 'INSERT INTO '.A11YC_TABLE_CHECKS.' (`url`, `code`, `uid`, `memo`) VALUES ';
				$sql.= '('.$esc_url.', '.Db::escapeStr($code).', '.$v['uid'].', '.$v['memo'].');';
				Db::execute($sql);
			}

			// leveling
			list($results, $checked, $passed_flat) = Evaluate::evaluate_url($url);
			$result = Evaluate::check_result($passed_flat);

			// update/create page
			$done = Db::escapeStr(isset($_POST['done']));
			$date = Db::escapeStr(date('Y-m-d'));
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

		// form
		$html = '';
		if ($url == 'bulk')
		{
			$html.= '<form action="'.A11YC_BULK_URL.'" method="POST">';
		}
		else
		{
			$html.= '<form action="'.A11YC_CHECKLIST_URL.$url.'" method="POST">';
		}
		$html.= static::form($url, $users, $current_user['id']);
		$html.= '<input type="submit" value="submit" />';
		$html.= '</form>';

		$title = $url == 'bulk' ? A11YC_LANG_BULK_TITLE : A11YC_LANG_CHECKLIST_TITLE ;

		return array($title, $html);
	}

	/**
	 * form html
	 * need to wrap <form> and add a submit button
	 * this method also used by bulk
	 *
	 * @param   string     $url
	 * @param   array      $users
	 * @param   integer    $current_user
	 * @return  string
	 */
	public static function form($url, $users = array(), $current_user_id = null)
	{
		$yml = Yaml::fetch();
		$standards = Yaml::each('standards');
		$setup = Setup::fetch_setup();
		$target_level = intval(@$setup['target_level']);
		$page = static::fetch_page($url);

		// dbio
		static::dbio($url);

		// value
		$bulk = Bulk::fetch_results();
		$cs = $url != 'bulk' ? Evaluate::fetch_results($url) : $bulk;
		$page = static::fetch_page($url);

		// html
		$html = '';
		$html = '<div id="a11yc_checks" data-a11yc-current-user="'.$current_user_id.'">';

		$html.= '<p id="a11yc_narrow_level" class="a11yc_hide_if_no_js">Level: ';
		$level_strs = array(
			'A'   => 'l_a',
			'AA'  => 'l_a,l_aa',
			'AAA' => 'l_a,l_aa,l_aaa'
		);
		$i = 0;
		foreach ($level_strs as $level_label => $level_str)
		{
			$class_str = $target_level == ++$i ? ' class="current"' : '';
			$html.= '<a role="button" tabindex="0" data-narrow-level="'.$level_str.'"'.$class_str.'>'.$level_label.'</a>';
		}
		$html.= '</p>';

		//header
		$html.= '<div id="a11yc_header">';

		$html.= '<p id="a11yc_info">'.A11YC_LANG_CHECKLIST_RESTOFNUM.':<span></span></p>';
		$html.= '<p><a href="'.Util::s(urldecode($url)).'">back to target page</a></p>';

		// level
		$html.= '<p>'.A11YC_LANG_TARGET_LEVEL.': '.Util::num2str($target_level).'</p>';
		$current_level = $target_level ? Evaluate::result_str($page['level'], $target_level) : '-';
		$html.= '<p>'.A11YC_LANG_CURRENT_LEVEL.': '.$current_level.'</p>';

		// not for bulk
		if ($url != 'bulk')
		{
			// standard
			$html.= '<p><label for="a11yc_standard">'.A11YC_LANG_STANDARD.'</label>';
			$html.= '<select name="standard" id="a11yc_standard">';
			foreach ($standards['standards'] as $k => $v)
			{
				$selected = $k == $page['standard'] ? ' selected="selected"' : '';
				$html.= '<option'.$selected.' value="'.$k.'">'.$v.'</option>';
			}
			$html.= '</select></p>';

			// is done
			$checked = @$page['done'] ? ' checked="checked"' : '';
			$html.= '<p><label for="a11yc_done">'.A11YC_LANG_CHECKLIST_DONE.': <input type="checkbox" name="done" id="a11yc_done" value="1"'.$checked.' /></label></p>';

			// check
			$errs = static::validate_page($url);
			if ($errs)
			{
				$html.= '<ul style="height: 200px; overflow: auto; border: 1px #aaa solid;">';
				foreach ($errs as $err)
				{
					$html.= '<li>'.$err.'</li>';
				}
				$html.= '</ul>';
			}
			else
			{
				$html.= A11YC_LANG_CHECKLIST_NOT_FOUND_ERR;
			}
		}
		else
		{
			$html.= '<div><label for="a11yc_update_all">'.A11YC_LANG_BULK_UPDATE.'</label>';
			$html.= '<select name="update_all" id="a11yc_update_all">';
			$html.= '<option value="1">'.A11YC_LANG_BULK_UPDATE1.'</option>';
			$html.= '<option value="2">'.A11YC_LANG_BULK_UPDATE2.'</option>';
			$html.= '<option value="3">'.A11YC_LANG_BULK_UPDATE3.'</option>';
			$html.= '</select></div>';

			$html.= '<div><label for="a11yc_update_done">'.A11YC_LANG_BULK_DONE.'</label>';
			$html.= '<select name="update_done" id="a11yc_update_done">';
			$html.= '<option value="1">'.A11YC_LANG_BULK_DONE1.'</option>';
			$html.= '<option value="2">'.A11YC_LANG_BULK_DONE2.'</option>';
			$html.= '<option value="3">'.A11YC_LANG_BULK_DONE3.'</option>';
			$html.= '</select></div>';
		}

		// menu
		$html.= '<ul id="a11yc_menu_principles">';
		foreach ($yml['principles'] as $v)
		{
			$html.= '<li id="a11yc_menuitem_'.$v['code'].'"><a href="#p_'.$v['code'].'">'.$v['code'].' '.$v['name'].'</a></li>';
		}
		$html.= '</ul><!--/#a11yc_menu_principles-->';

		$html.= '</div><!--/#a11yc_header-->';

		foreach ($yml['principles'] as $k => $v)
		{
			// principles
			$html.= '<div id="section_p_'.$v['code'].'" class="section_guidelines"><h2 id="p_'.$v['code'].'" tabindex="-1">'.$v['code'].' '.$v['name'].'</h2>';

			// guidelines
			foreach ($yml['guidelines'] as $kk => $vv)
			{
				if ($kk{0} != $k) continue;
				$html.= '<div id="g_'.$vv['code'].'" class="section_guideline"><h3>'.Util::key2code($vv['code']).' '.$vv['name'].'</h3>';

				// criterions
				$html.='<div class="section_criterions">';
				foreach ($yml['criterions'] as $kkk => $vvv)
				{
					if (substr($kkk, 0, 3) != $kk) continue;
					$html.= '<div id="c_'.$kkk.'" class="section_criterion l_'.strtolower($vvv['level']['name']).'">';
					$html.= '<div class="a11yc_criterion">';
					$html.= '<h4>'.Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')';
					if (isset($vvv['url_as']))
					{
						$html.= '<a'.A11YC_TARGET.' href="'.$vvv['url_as'].'" class="link_as">Accessibility Supported</a>';
					}

					$html.= '<a'.A11YC_TARGET.' href="'.$vvv['url'].'" class="link_understanding">Understanding</a></h4>';

					$html.= '<p>'.$vvv['summary'].'</p></div><!-- /.a11yc_criterion -->';

					// checks
					$html.= '<table><tbody>';
					foreach ($yml['checks'][$kkk] as $code => $val)
					{
						$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : '';
						$passes = array();
						if (isset($val['pass']))
						{
							foreach ($val['pass'] as $pass_code => $pass_each)
							{
								$passes = array_merge($passes, $pass_each);
							}
						}
						$data = $passes ? ' data-pass="'.join(',', $passes).'"' : '';

						$checked = '';
						if (
							($page && isset($cs[$code])) ||
							( ! $page && isset($bulk[$code]))
						)
						{
							$checked = ' checked="checked"';
						}

						$html.= '<tr'.$non_interference.'>';

						$html.= '<th>';
						$html.= '<label for="'.$code.'"><input type="checkbox"'.$checked.' id="'.$code.'" name="chk['.$code.'][on]" value="1" '.$data.' class="'.$vvv['level']['name'].'" />';
						$html.= $val['name'].'</label>';
						$html.= '</th>';

						$html.= '<td style="white-space: nowrap;width:5em;">';
						$memo = isset($cs[$code]['memo']) ? $cs[$code]['memo'] : @$bulk[$code]['memo'] ;
						$html.= '<textarea name="chk['.$code.'][memo]">'.$memo.'</textarea>';
						$html.= '</td>';

						$html.= '<td style="white-space: nowrap;width:5em;">';
						$html.= '<select name="chk['.$code.'][uid]">';
						foreach ($users as $uid => $name)
						{
							$selected = '';
							if (
								isset($cs[$code]['uid']) && $cs[$code]['uid'] = $uid ||
								isset($bulk[$code]['uid']) && $bulk[$code]['uid'] = $uid
							)
							{
								$selected = ' selected="selected"';
							}
							$html.= '<option value="'.$uid.'">'.$name.'</option>';
						}
						$html.= '</select>';
						$html.= '</td>';
						$html.= '<td style="white-space: nowrap;width:5em;">';
						$html.= '<a'.A11YC_TARGET.' href="'.A11YC_DOC_URL.$code.'&amp;criterion='.$kkk.'">how to</a>';
						$html.= '</td>';
						$html.= '</tr>';
					}
					$html.= '</tbody></table>';
					$html.= '</div><!--/#c_'.$kkk.'.l_'.strtolower($vvv['level']['name']).'-->';
				}
				$html.='</div><!--/.section_criterions-->';
				$html.='</div><!--/#g_'.$vv['code'].'-->';
			}
			$html.= '</div><!--/#section_p_'.$v['code'].'.section_guidelines-->';
		}
		$html.= '<input type="hidden" value="'.htmlspecialchars($url, ENT_QUOTES).'" />';
		$html.= '</div><!-- /#a11yc_checks -->';
		return $html;
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
		$html.= '<p>'.A11YC_LANG_CHECKLIST_ACHIEVEMENT.': '.evaluate::result_str($result, $target_level).'</p>';

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
		$yml = Yaml::fetch();

		$html = '';
		$html.= '<table>';
		$html.= '<thead>';
		$html.= '<tr>';
		$html.= '<th colspan="2">'.A11YC_LANG_CRITERION.'</th>';
		$html.= '<th>'.A11YC_LANG_LEVEL.'</th>';
		$html.= '<th>'.A11YC_LANG_EXIST.'</th>';
		$html.= '<th>'.A11YC_LANG_PASS.'</th>';
		$html.= '</tr>';
		$html.= '</thead>';

		foreach ($yml['criterions'] as $k => $v)
		{
			if (
				($include && strlen($v['level']['name']) <= $target_level) ||
				( ! $include && strlen($v['level']['name']) > $target_level))
			{
				$html.= '<tr>';
				$html.= '<th>'.Util::key2code($k).'</th>';
				$html.= '<td>'.$v['name'].'</td>';
				$html.= '<td>'.$v['level']['name'].'</td>';
				$html.= '<td>';
				$html.= isset($results[$k]['non_exist']) ? A11YC_LANG_EXIST_NON : '-';
				$html.= '</td>';
				$html.= '<td>';
				$html.= $results[$k]['pass'] ? A11YC_LANG_PASS : '-';
				$html.= '</td>';
				$html.= '</tr>';
			}

		}
		$html.= '</table>';
		$html.= '</section>';
		return $html;
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
