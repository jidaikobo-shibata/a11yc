<?php namespace A11yc; ?>
<div id="a11yc_checks" data-a11yc-target_level="<?php echo str_repeat('a',$target_level) ?>" data-a11yc-additional_criterions='[<?php echo $additional_criterions ? '"'.$additional_criterions.'"' : ''?>]' data-a11yc-current-user="<?php echo $current_user_id ?>" data-a11yc-lang='{"expand":"<?php echo A11YC_LANG_CTRL_EXPAND ?>", "compress": "<?php echo A11YC_LANG_CTRL_COMPRESS ?>", "conformance": "<?php echo A11YC_LANG_CHECKLIST_CONFORMANCE.','.A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL ?>"}'>

<?php
function a11yc_implement_checklist_row($id, $criterion, $arr, $cs, $yml, $refs)
{
	$html = '';
	$html.= '<dl><dt>'.htmlspecialchars_decode($arr[$criterion][$id]['title']).'</dt>';
	$html.= '<dd><ul>';

	$li = '';
	if ( ! empty($arr[$criterion][$id]['techs']))
	{
		foreach ($arr[$criterion][$id]['techs'] as $implement)
		{
			if ( ! isset($yml['techs'][$implement]['title'])) continue;
			$idfor = $criterion.'_'.$id.'_'.$implement;
			$checked = isset($cs[$criterion]) && in_array($implement, $cs[$criterion]) ?
							 ' checked="checked"' :
							 '';

			$li.= '<li>';
			$li.= '<label for="'.$idfor.'" class="a11yc_checkitem"><input type="checkbox"'.$checked.' id="'.$idfor.'" name="chk['.$criterion.'][]" value="'.$implement.'" /><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$yml['techs'][$implement]['title'].'</label>';
			$li.= '<a'.A11YC_TARGET.' href="'.$refs['t'].$implement.'.html" title="'.A11YC_LANG_DOC_TITLE.'" class="a11yc_link_howto"><span role="presentation" aria-hidden="true" class="a11yc_icon_fa a11yc_icon_howto"></span><span class="a11yc_skip"><?php echo A11YC_LANG_DOC_TITLE ?></span></a>';
			$li.= '</li>';
		}
	}
	else
	{
		$idfor = $criterion.'_'.$id;
		$checked = isset($cs[$criterion]) && in_array($id, $cs[$criterion]) ?
						 ' checked="checked"' :
						 '';

		$li.= '<li>';
		$li.= '<label for="'.$idfor.'" class="a11yc_checkitem"><input type="checkbox"'.$checked.' id="'.$idfor.'" name="chk['.$criterion.'][]" value="'.$id.'" /><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_IMPLEMENT_CHECK.'</label>';
		$li.= '</li>';
	}
	$html.= $li.'</ul></dd></dl>';
	return $html;
}
?>

<!-- header -->
<div id="a11yc_header">
	<div id="a11yc_header_inner" class="postbox">
		<!-- not for bulk -->
		<?php if ( ! $is_bulk): ?>
		<div id="a11yc_targetpage_data">
			<!-- target page -->
			<table id="a11yc_targetpage_info">
			<tr>
				<th class=""><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
				<td title="<?php echo $target_title ?>"><?php echo $target_title ?></td>
			</tr>
			<tr>
				<th class=""><?php echo A11YC_LANG_PAGE_URLS ?></th>
				<td title="<?php echo Util::s(Util::urldec($url)) ?>"><?php echo '<a href="'.Util::s(Util::urldec($url)).'">'.Util::s(Util::urldec($url)).'</a>' ?></td>
	<?php /* ?>
	<?php // 振る舞いが怪しいので、ちょっと様子見 ?>
				<th><label for="a11yc_mod_url"><?php echo A11YC_LANG_PAGE_URLS ?></label></th>
				<td>
					<input type="text" name="mod_url" id="a11yc_mod_url" size="30" value="<?php echo Util::urldec($url) ?>" />
				</td>
	<?php */ ?>
			</tr>
			</table>
		</div><!-- /#a11yc_targetpage_data -->
		<?php endif; ?>

		<!-- a11yc menu -->
		<ul id="a11yc_menu_principles">
		<?php foreach ($yml['principles'] as $v): ?>
			<li id="a11yc_menuitem_<?php echo $v['code'] ?>"><a href="#a11yc_header_p_<?php echo $v['code'] ?>"><span><?php echo /* $v['code'] .' '.*/ $v['name'] ?></span></a></li>
		<?php endforeach; ?>
		</ul><!--/#a11yc_menu_principles-->
	</div><!--/#a11yc_header_inner-->
</div><!--/#a11yc_header-->


		<div id="a11yc_header_ctrl" class="">
		<?php if ( ! $is_bulk): ?>
			<!-- standard -->
			<p id="a11yc_header_done_date" class="">
				<label for="a11yc_done_date"><?php echo A11YC_LANG_TEST_DATE ?></label>
				<input type="text" name="done_date" id="a11yc_done_date" size="10" value="<?php echo $done_date ?>" />
			</p>
			<!-- standard -->
			<p id="a11yc_header_select_standard" class="">
				<label for="a11yc_standard"><?php echo A11YC_LANG_STANDARD ?></label>
				<select name="standard" id="a11yc_standard">
				<?php
				foreach ($standards as $k => $v):
					$selected = $k == @$page['standard'] ? ' selected="selected"' : '';
				?>
					<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
				<?php endforeach; ?>
				</select>
			</p>

			<!-- selection reason -->
			<p id="a11yc_header_selection_reason" class="">
				<label for="a11yc_selection_reason"><?php echo A11YC_LANG_CANDIDATES_REASON ?></label>
				<select name="selection_reason" id="a11yc_selection_reason">
				<?php
				foreach ($selection_reasons as $k => $v):
					$selected = $k == Arr::get($page, 'selection_reason') ? ' selected="selected"' : '';
				?>
					<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		<?php else: ?>
			<p><label for="a11yc_update_all"><?php echo A11YC_LANG_BULK_UPDATE ?></label>
			<select name="update_all" id="a11yc_update_all" >
				<option value="1"><?php echo A11YC_LANG_BULK_UPDATE1 ?></option>
				<option value="2"><?php echo A11YC_LANG_BULK_UPDATE2 ?></option>
				<option value="3"><?php echo A11YC_LANG_BULK_UPDATE3 ?></option>
			</select></p>

			<p><label for="a11yc_update_done"><?php echo A11YC_LANG_BULK_DONE ?></label>
			<select name="update_done" id="a11yc_update_done">
				<option value="1"><?php echo A11YC_LANG_BULK_DONE1 ?></option>
				<option value="2"><?php echo A11YC_LANG_BULK_DONE2 ?></option>
				<option value="3"><?php echo A11YC_LANG_BULK_DONE3 ?></option>
			</select></p>
		<?php endif; ?>
		</div><!-- /#a11yc_header_ctrl -->
		<div id="a11yc_header_right" class="">
		<?php if ( ! $is_bulk): ?>
			<!-- level -->
			<p id="a11yc_target_level" class="a11yc_hide_if_fixedheader"><?php echo A11YC_LANG_TARGET_LEVEL ?>: <?php echo Util::num2str($target_level) ?>
	<?php $current_level = $target_level ? Evaluate::resultStr(@$page['level'], $target_level) : '-'; ?><br><?php echo A11YC_LANG_CURRENT_LEVEL ?>: <span id="a11yc_conformance_level"><?php echo $current_level ?></span></p>
		<?php endif ?>
		</div><!-- /#a11yc_header_right -->

		<?php
			if ( ! $is_bulk):
				// validation
				echo $validation_result;
			endif;
		?>

<?php
	$fcnt = 0;
	foreach ($yml['principles'] as $k => $v):
?>
	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue;
	?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><h3 class="a11yc_header_guideline"><?php echo Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<?php
		foreach ($yml['criterions'] as $criterion => $vvv):

			// check target level and additional_criterions
			if (
				! in_array($vvv['code'], Model\Setting::fetch('additional_criterions')) &&
				intval($target_level) < strlen($vvv['level']['name'])
			) continue;

			if (substr($criterion, 0, 3) != $kk) continue;
			$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
			$skip_non_interference = isset($vvv['non-interference']) ? '<span class="a11yc_skip">&nbsp;('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</span>' : '';
			$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
			$class_str.= ' a11yc_level_'.strtolower($vvv['level']['name']);
			$class_str.= ' a11yc_p_'.$k.'_criterion';
		?>
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
					<th class="a11yc_table_check_memo" scope="col"><label for="results_<?php echo $criterion; ?>_memo"><?php echo A11YC_LANG_OPINION ?></label></th>
					<th class="a11yc_table_check_user" scope="col"><label for="results[<?php echo $criterion ?>][uid]"><?php echo A11YC_LANG_CTRL_PERSONS ?></label></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="a11yc_table_check_test_result">
							<fieldset>
								<?php
								foreach (Values::resultsOptions() as $rk => $rv):
									$selected = isset($results[$criterion]['result']) && intval($results[$criterion]['result']) == $rk ? ' checked="checked"' : '';
									echo '<input type="radio" name="results['.$criterion.'][result]" id="results['.$criterion.'][result]_'.$rk.'"'.$selected.' value="'.$rk.'" class="a11yc_skip"><label class="a11yc_checkitem" for="results['.$criterion.'][result]_'.$rk.'"><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$rv.'</label>';
								endforeach;
								?>
							</fieldset>
						</td>
						<td class="a11yc_table_check_test_method">
							<fieldset>
								<?php
								foreach (Values::testMethodsOptions() as $rk => $rv):
									$selected = isset($results[$criterion]['method']) && intval($results[$criterion]['method']) == $rk ? ' checked="checked"' : '';
									echo '<input type="radio" name="results['.$criterion.'][method]" id="results['.$criterion.'][method]_'.$rk.'"'.$selected.' value="'.$rk.'" class="a11yc_skip"><label class="a11yc_checkitem" for="results['.$criterion.'][method]_'.$rk.'"><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span>'.$rv.'</label>';
								endforeach;
								?>
							</fieldset>
						</td>
						<td class="a11yc_table_check_memo">
							<textarea name="results[<?php echo $criterion; ?>][memo]" id="results_<?php echo $criterion; ?>_memo" rows="3"><?php echo Util::s(Arr::get($results, "{$criterion}.memo")); ?></textarea>
						</td>
						<td class="a11yc_table_check_user">
							<select name="results[<?php echo $criterion ?>][uid]" id="results[<?php echo $criterion ?>][uid]">
					<?php
					foreach ($users as $uid => $name):
						if ($is_new):
							$selected = $current_user_id == $uid ? ' selected="selected"' : '';
						else:
							$selected = Arr::get($results, "{$criterion}.uid") == $uid ? ' selected="selected"' : '';
						endif;
					?>
						<option value="<?php echo $uid ?>"<?php echo $selected ?>><?php echo $name ?></option>
					<?php endforeach; ?>
					</select>
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
			if ($issues[$criterion]):
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

$iclusings = array_map('intval', $iclusings);

// situation exists
$html = '';
if (isset($iclsits[$criterion]) && $iclsits[$criterion]):
// using icl?
	foreach ($iclsits[$criterion] as $iclsit):
		if (count(array_diff($iclsit['implements'], $iclusings)) == count($iclsit['implements'])) continue;

		$html.= '<tr><th>'.$iclsit['title'].'</th></tr>';
		foreach ($iclsit['implements'] as $icl):
			if ( ! in_array($icl, $iclusings)) continue;
			$html.= '<tr><td>'.a11yc_implement_checklist_row($icl, $criterion, $icls, $cs, $yml, $refs).'</td></tr>';
		endforeach;
	endforeach;
elseif (isset($icls[$criterion])):
	foreach ($icls[$criterion] as $id => $v):
		if ( ! in_array($id, $iclusings)) continue;
		$html.= '<tr><td>'.a11yc_implement_checklist_row($id, $criterion, $icls, $cs, $yml, $refs).'</td></tr>';
	endforeach;
endif;

if ( ! empty($html)):
?>

			<details class="a11yc_check_disclosure">
				<summary><?php echo A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE ?></summary>

				<table class="a11yc_table a11yc_table_check"><tbody>
					<?php echo $html; ?>
				</tbody></table>
			</details>
<?php endif; ?>

			</div><!--/#c_<?php echo $criterion ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<input type="hidden" name="page_title" value="<?php echo Util::s($target_title) ?>" />
<input type="hidden" name="url" value="<?php echo Util::s($url) ?>" />

</div><!-- /#a11yc_checks -->