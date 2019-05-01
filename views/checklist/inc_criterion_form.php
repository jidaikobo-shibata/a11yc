<?php
namespace A11yc;

$statuses = Values::issueStatus();
$icls     = Model\Icl::fetchAll(true);
$icltree  = Model\Icl::fetchTree(true);

$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
$skip_non_interference = isset($vvv['non-interference']) ? '<span class="a11yc_skip">&nbsp;('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</span>' : '';
$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
$class_str.= ' a11yc_level_'.strtolower($vvv['level']['name']);
$class_str.= ' a11yc_p_'.$vvv['guideline']['principle']['code'].'_criterion';
?>
<!-- criterions -->
<div id="a11yc_c_<?php echo $criterion ?>" class="a11yc_section_criterion<?php echo $class_str ?>">
	<h4 class="a11yc_header_criterion"><?php echo Util::key2code($vvv['code']).' '.$vvv['name'].' <span class="a11yc_header_criterion_level">('.$vvv['level']['name'].$non_interference.')</span>' ?></h4>
	<ul class="a11yc_outlink">
	<?php if (isset($vvv['url_as'])): ?>
		<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
	<?php endif; ?>
		<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
	</ul>
	<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>

	<!-- .a11yc_result -->
	<div class="a11yc_result">
		<table class="a11yc_table a11yc_table_check">
		<thead>
		<tr>
			<th class="a11yc_table_check_test_result" scope="col"><?php echo A11YC_LANG_TEST_RESULT ?></th>
			<th class="a11yc_table_check_test_method" scope="col"><?php echo A11YC_LANG_TEST_METHOD ?></th>
			<th class="a11yc_table_check_memo" scope="col"><label for="results_<?php echo $page_id ?>_<?php echo $criterion; ?>_memo"><?php echo A11YC_LANG_OPINION ?></label></th>
			<th class="a11yc_table_check_user" scope="col"><label for="results_<?php echo $page_id ?>_<?php echo $criterion ?>_uid"><?php echo A11YC_LANG_CTRL_PERSONS ?></label></th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="a11yc_table_check_test_result">
					<?php
					$results_options = Values::resultsOptions();
					if (in_array($focus, array('all', 'result')) || Input::get('integrate') == 1):
					?>
					<fieldset>
					<?php

					foreach ($results_options as $rk => $rv):
						$selected = isset($results[$criterion]['result']) && intval($results[$criterion]['result']) == $rk ? ' checked="checked"' : '';
						echo '<input type="radio" name="results['.$page_id.']['.$criterion.'][result]" id="results_'.$page_id.'_'.$criterion.'_result_'.$rk.'"'.$selected.' value="'.$rk.'" class="a11yc_skip a11yc_check_conformance"><label class="a11yc_checkitem" for="results_'.$page_id.'_'.$criterion.'_result_'.$rk.'"><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$rv.'</label>';
					endforeach;
					?>
					</fieldset>
					<?php
					elseif (isset($results[$criterion]['result'])):
						$rk = intval($results[$criterion]['result']);
						echo $results_options[$rk];
					else:
						echo $results_options[0];
					endif;
					?>
				</td>
				<td class="a11yc_table_check_test_method">
					<?php
					$test_methods_options = Values::testMethodsOptions();
					if (in_array($focus, array('all', 'result')) || Input::get('integrate') == 1):
					?>
					<fieldset>
					<?php
					foreach ($test_methods_options as $rk => $rv):
						$selected = isset($results[$criterion]['method']) && intval($results[$criterion]['method']) == $rk ? ' checked="checked"' : '';
						echo '<input type="radio" name="results['.$page_id.']['.$criterion.'][method]" id="results_'.$page_id.'_'.$criterion.'_method_'.$rk.'"'.$selected.' value="'.$rk.'" class="a11yc_skip a11yc_check_method"><label class="a11yc_checkitem" for="results_'.$page_id.'_'.$criterion.'_method_'.$rk.'"><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$rv.'</label>';
					endforeach;
					?>
					</fieldset>
					<?php
					elseif (isset($results[$criterion]['method'])):
						$rk = intval($results[$criterion]['method']);
						echo $test_methods_options[$rk];
					else:
						echo $test_methods_options[0];
					endif;
					?>
				</td>
				<td class="a11yc_table_check_memo">
					<?php
					$memo = Util::s(Arr::get($results, "{$criterion}.memo"));
					$test_methods_options = Values::testMethodsOptions();
					if (in_array($focus, array('all', 'result')) || Input::get('integrate') == 1):
					?>
					<textarea name="results[<?php echo $page_id ?>][<?php echo $criterion; ?>][memo]"'.$disabled.' id="results_<?php echo $page_id; ?>_<?php echo $criterion; ?>_memo" rows="3"><?php echo $memo; ?></textarea>
					<?php
					else:
						echo $memo;
					endif;
					?>
				</td>
				<td class="a11yc_table_check_user">
					<?php
					$current_uid = Arr::get($results, "{$criterion}.uid", 0);
					$test_methods_options = Values::testMethodsOptions();
					if (in_array($focus, array('all', 'result')) || Input::get('integrate') == 1):
					?>
					<select name="results[<?php echo $page_id ?>][<?php echo $criterion ?>][uid]" id="results_<?php echo $page_id; ?>_<?php echo $criterion ?>_uid">
					<?php
					foreach ($users as $uid => $name):
						if ($is_new):
							$selected = $current_user_id == $uid ? ' selected="selected"' : '';
						else:
							$selected = $current_uid == $uid ? ' selected="selected"' : '';
						endif;
					?>
					<option value="<?php echo $uid ?>"<?php echo $selected ?>><?php echo $name ?></option>
					<?php endforeach; ?>
					</select>
					<?php
					else:
						echo Arr::get($users, $current_uid, $results_options[0]);
					endif;
					?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- /.a11yc_result -->

	<!-- a11yc_issues -->
	<div class="a11yc_issues">
		<a href="<?php echo A11YC_ISSUE_URL.'add&amp;url='.Util::urlenc($url).'&amp;criterion='.$criterion ?>" target="_blank"><?php echo A11YC_LANG_ISSUE_ADD ?></a>

		<?php
		if (isset($issues[$criterion])):
			echo '<ul>';
			foreach ($issues[$criterion] as $issue):
				echo '<li><a href="'.A11YC_ISSUE_URL.'read&amp;id='.intval($issue['id']).'" target="_blank">'.nl2br(Util::s($issue['id'].': '.$issue['error_message'])).' ['.$statuses[$issue['status']].']</a></li>';
			endforeach;
			echo '</ul>';
		endif;
		?>

	</div>
	<!-- /.a11yc_issues -->

	<?php
	// implement checklist
	$html = '';
	$html.= '<div class="a11yc_implement_checklist">';
	$icl_options = Values::iclOptions();

	foreach (Arr::get($icltree, $criterion, array()) as $parent_id => $ids):
		if (isset($icls[$parent_id])):
			$html.= '<h2 class="a11yc_implement_heading">'.$icls[$parent_id]['title'].'</h2>';
		endif;

		foreach ($ids as $id):
			if ( ! isset($icls[$id])) continue;
			if ( ! in_array($id, $settings['icl'])) continue;

			$val = $icls[$id];
			$iclchk = Arr::get($iclchks, $id, 1);

			if (in_array($focus, array('all', 'icl')) || Input::get('integrate') == 1):
				$html.= '<fieldset><legend>';
				$html.= strip_tags($val['title_short']);
				$html.= '</legend>';
				$html.= '<a href="'.A11YC_ICL_URL.'read&amp;id='.intval($id).'" target="_blank">'.A11YC_LANG_CTRL_VIEW.'</a>';

				foreach ($icl_options as $ik => $iv):
					$selected = $iclchk == $ik ? ' checked="checked"' : '';

					$html.= '<input type="radio" id="icl_'.$page_id.'_'.$id.'_'.$ik.'" class="a11yc_skip a11yc_check_conformance" name="iclchks['.$page_id.']['.$id.']"'.$selected.' value="'.$ik.'"><label for="icl_'.$page_id.'_'.$id.'_'.$ik.'" class="a11yc_checkitem"><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$iv.'</label>';
				endforeach;
			else:
				$html.= '<dl><dt>'.strip_tags($val['title_short']).'</dt>';
				$html.= '<dd>'.$icl_options[$iclchk].'</dd></dl>';
			endif;

			$html.= '<table class="a11yc_table a11yc_implement_checklist_each a11yc_table_check"><tbody>';
			$techs = '';

			// tech exists
			if ( ! empty($val['techs'])):
				foreach ($val['techs'] as $implement):
					if ( ! isset($yml['techs'][$implement]['title'])) continue;
					$idfor = $page_id.'_'.$criterion.'_'.$id.'_'.$implement;

					$checked = in_array($implement, Arr::get($cs, $criterion, array())) ? ' checked="checked"' : ''; // Transitional
					$checked = in_array($implement, Arr::get($cs, $id, array())) ? ' checked="checked"' : $checked;

					$techs.= '<tr>';

					if (in_array($focus, array('all', 'check')) || Input::get('integrate') == 1):
						$techs.= '<th><label for="'.$idfor.'" class="a11yc_checkitem"><input type="checkbox"'.$checked.' id="'.$idfor.'" class="a11yc_skip" name="chk['.$page_id.']['.$id.'][]" value="'.$implement.'" /><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$yml['techs'][$implement]['title'].'</label></th>';
						$techs.= '<td class="a11yc_table_check_howto"><a'.A11YC_TARGET.' href="'.$refs['t'].$implement.'.html" title="'.A11YC_LANG_DOC_TITLE.'" class="a11yc_link_howto"><span role="presentation" aria-hidden="true" class="a11yc_icon_fa a11yc_icon_howto"></span><span class="a11yc_skip"><?php echo A11YC_LANG_DOC_TITLE ?></span></a></td>';
						$techs.= '</tr>';
					else:
						$checkstr = $checked ? 'o' : 'x';
						$html.= '<tr><th>'.$yml['techs'][$implement]['title'].'</th>';
						$html.= '<td>'.$checkstr.'</td></tr>';
					endif;
				endforeach;

			// tech is not exists
			else:

				$idfor = $page_id.'_'.$criterion.'_'.$id;
				$checked = in_array($id, Arr::get($cs, $criterion, array())) ? ' checked="checked"' : '';

				if (in_array($focus, array('all', 'check')) || Input::get('integrate') == 1):
					$techs.= '<tr>';
					$techs.= '<td colspan="2"><label for="'.$idfor.'" class="a11yc_checkitem"><input type="checkbox"'.$checked.' id="'.$idfor.'" class="a11yc_skip" name="chk['.$page_id.']['.$criterion.'][]" value="'.$id.'" /><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_IMPLEMENT_CHECK.'</label></td>';
					$techs.= '</tr>';
				else:
					$checkstr = $checked ? 'o' : 'x';
					$html.= '<tr><th>'.$yml['techs'][$implement]['title'].'</th>';
					$html.= '<td>'.$checkstr.'</td></tr>';
				endif;

			endif;
			$html.= $techs.'</tbody></table>';
			$html.= '</fieldset>';
		endforeach;
	endforeach;
	$html.= '</div><!-- /.a11yc_implement_checklist -->';

	if ( ! empty($html)): ?>
		<details class="a11yc_check_disclosure">
			<summary><h1><?php echo A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE ?></h1></summary>
			<?php echo $html; ?>
		</details>
	<?php endif; ?>

	<?php
	// failure
	$html = '';
	if (isset($yml['techs_codes'][$criterion]['f'])):
		$techs = '';
		foreach ($yml['techs_codes'][$criterion]['f'] as $v):
			$checked = in_array($v, Arr::get($cs, $criterion, array())) ? ' checked="checked"' : '';
			$idfor = $page_id.'_'.$criterion.'_'.$v;

			if (in_array($focus, array('all', 'failure')) || Input::get('integrate') == 1):
				$techs.= '<tr><td>';
				$techs.= '<label for="'.$idfor.'" class="a11yc_checkitem"><input type="checkbox"'.$checked.' id="'.$idfor.'" class="a11yc_skip" name="chk['.$page_id.']['.$criterion.'][]" value="'.$v.'" /><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$yml['techs'][$v]['title'].'</label></td></tr>';
			else:
				$checkstr = $checked ? 'o' : 'x';
				$techs.= '<tr><th>'.$yml['techs'][$v]['title'].'</th>';
				$techs.= '<td>'.$checkstr.'</td></tr>';
			endif;
		endforeach;
		if ( ! empty($techs)):
			$html.= '<table class="a11yc_table a11yc_failure_checklist_each a11yc_table_check"><tbody>';
			$html.= $techs;
			$html.= '</tbody></table>';
		endif;
	endif;

	if ( ! empty($html)):
	?>
		<details class="a11yc_check_disclosure">
			<summary><h1><?php echo A11YC_LANG_CHECKLIST_NG_REASON ?></h1></summary>
			<div class="a11yc_failure_checklist">
			<?php echo $html; ?>
			</div><!-- .a11yc_failure_checklist -->
		</details>
	<?php endif; ?>
</div><!--/#c_<?php echo $criterion ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
