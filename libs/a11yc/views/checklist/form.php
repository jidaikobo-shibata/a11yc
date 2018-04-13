<?php namespace A11yc; ?>
<div id="a11yc_checks" data-a11yc-target_level="<?php echo str_repeat('a',$target_level) ?>" data-a11yc-additional_criterions='[<?php echo $additional_criterions ? '"'.$additional_criterions.'"' : ''?>]' data-a11yc-current-user="<?php echo $current_user_id ?>" data-a11yc-lang='{"expand":"<?php echo A11YC_LANG_CTRL_EXPAND ?>", "compress": "<?php echo A11YC_LANG_CTRL_COMPRESS ?>", "conformance": "<?php echo A11YC_LANG_CHECKLIST_CONFORMANCE.','.A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL ?>"}' <?php if($checklist_behaviour) echo ' class="a11yc_hide_passed_item"' ?>>

<!-- header -->
<div id="a11yc_header">
	<div id="a11yc_header_inner">
		<div id="a11yc_header_ctrl" class="a11yc_hide_if_fixedheader">
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
		<?php endif; ?>
		</div><!-- /#a11yc_header_ctrl -->
		<div id="a11yc_header_left" class="a11yc_fl">
			<!-- not for bulk -->
		<?php if ( ! $is_bulk): ?>
			<div id="a11yc_targetpage_data">
			<!-- target page -->
		<table id="a11yc_targetpage_info">
		<tr>
			<th class="a11yc_hide_if_fixedheader"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
			<td><?php echo $target_title ?></td>
		</tr>
		<tr>
			<th class="a11yc_hide_if_fixedheader"><?php echo A11YC_LANG_PAGES_URLS ?></th>
			<td><?php echo '<a href="'.Util::s(Util::urldec($url)).'">'.Util::s(Util::urldec($url)).'</a>' ?></td>
<?php /* ?>
<?php // 振る舞いが怪しいので、ちょっと様子見 ?>
			<th><label for="a11yc_mod_url"><?php echo A11YC_LANG_PAGES_URLS ?></label></th>
			<td>
				<input type="text" name="mod_url" id="a11yc_mod_url" size="30" value="<?php echo Util::urldec($url) ?>" />
			</td>
<?php */ ?>
		</tr>
		</table>

		<?php
			// validation
			echo $validation_result;
		?>

		</div><!-- /#a11yc_targetpage_data -->
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
		</div><!-- /#a11yc_header_left -->

		<div id="a11yc_header_right" class="a11yc_fr">
		<!-- narrow level -->
			<p class="a11yc_narrow_level" data-a11yc-narrow-target=".a11yc_section_principle">Level:
		<?php
			for ($i=1; $i<=3; $i++)
			{
				$class_str = ($i == $target_level) || ($target_level == 0 && $i == 3) ? ' class="current"' : '';
				echo '<a role="button" tabindex="0" data-narrow-level=\'['.implode(',', array_slice(array('"l_a"', '"l_aa"', '"l_aaa"'), 0, $i)).']\''.$class_str.'>'.Util::num2str($i).'</a>';
			}
		?>
			</p>

		<?php if ( ! $is_bulk): ?>
			<!-- level -->
			<p id="a11yc_target_level" class="a11yc_hide_if_fixedheader"><?php echo A11YC_LANG_TARGET_LEVEL ?>: <?php echo Util::num2str($target_level) ?>
	<?php $current_level = $target_level ? Evaluate::resultStr(@$page['level'], $target_level) : '-'; ?><br><?php echo A11YC_LANG_CURRENT_LEVEL ?>: <span id="a11yc_conformance_level"><?php echo $current_level ?></span></p>
		<?php endif ?>
		<?php /* ?>
			<!-- rest of num -->
			<p class="a11yc_hide_if_no_js"><a role="button" class="a11yc_disclosure"><?php echo A11YC_LANG_CHECKLIST_RESTOFNUM ?>&nbsp;:&nbsp;<span id="a11yc_rest_total">&nbsp;-&nbsp;</span></a></p>
			<div class="a11yc_disclosure_target show a11yc_hide_if_fixedheader a11yc_hide_if_no_js">
			<table id="a11yc_rest">
				<thead>
					<tr>
						<th scope="col">&nbsp;</th>
						<th scope="col" class="a11yc_rest_label_lv">A</th>
						<th scope="col" class="a11yc_rest_label_lv">AA</th>
						<th scope="col" class="a11yc_rest_label_lv">AAA</th>
						<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TOTAL ?></th>
					</tr>
				</thead>
				<tbody>
			<?php foreach ($yml['principles'] as $v): ?>
					<tr id="a11yc_rest_<?php echo $v['code'] ?>">
						<th scope="row"><?php echo $v['code'].'&nbsp;'.$v['name'] ?></th>
						<td data-a11yc-rest-level="a">&nbsp;-&nbsp;</td>
						<td data-a11yc-rest-level="aa">&nbsp;-&nbsp;</td>
						<td data-a11yc-rest-level="aaa">&nbsp;-&nbsp;</td>
						<td class="a11yc_rest_subtotal">&nbsp;-&nbsp;</td>
					</tr>
			<?php endforeach; ?>
				</tbody>
			</table>
			</div>
			<?php */ ?>
			<?php /*
				$checked = $setup['checklist_behaviour'] ? ' checked="checked"' : '';
			?>
			<label for="a11yc_checklist_behaviour" class="a11yc_label_switch"><span role="presentation" aria-hidden="true"></span><input type="checkbox" name="checklist_behaviour" id="a11yc_checklist_behaviour" value=""<?php echo $checked ?> class="" /><?php echo A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR ?></label>
			<?php */ ?>
		</div><!-- /#a11yc_header_right -->
	</div><!--/#a11yc_header_inner-->

		<!-- a11yc menu -->
	<ul id="a11yc_menu_principles">
	<?php foreach ($yml['principles'] as $v): ?>
		<li id="a11yc_menuitem_<?php echo $v['code'] ?>"><a href="#a11yc_header_p_<?php echo $v['code'] ?>"><span><?php echo /* $v['code'] .' '.*/ $v['name'] ?></span></a></li>
	<?php endforeach; ?>
	</ul><!--/#a11yc_menu_principles-->

</div><!--/#a11yc_header-->

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
				<table class="a11yc_table_check">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo A11YC_LANG_TEST_RESULT ?>
						</th>

						<td>
							<label for="results_<?php echo $criterion; ?>_result" class="a11yc_skip"><?php echo A11YC_LANG_TEST_RESULT ?></label>
							<select name="results[<?php echo $criterion; ?>][result]" id="results_<?php echo $criterion; ?>_result">
							<?php
							foreach (Values::resultsOptions() as $rk => $rv):
								$selected = isset($results[$criterion]) && intval($results[$criterion]['result']) == $rk ? ' selected="selected"' : '';
								echo '<option'.$selected.' value="'.$rk.'">'.$rv.'</option>';
							endforeach;
							?>
							</select>
						</td>

						<td>
							<label for="results_<?php echo $criterion; ?>_test_method"><?php echo A11YC_LANG_TEST_METHOD ?></label>
							<select name="results[<?php echo $criterion; ?>][method]" id="results_<?php echo $criterion; ?>_method">
							<?php
							foreach (Values::testMethodsOptions() as $rk => $rv):
								$selected = isset($results[$criterion]) && intval($results[$criterion]['method']) == $rk ? ' selected="selected"' : '';
								echo '<option'.$selected.' value="'.$rk.'">'.$rv.'</option>';
							endforeach;
							?>

							</select>
						</td>

						<td class="a11yc_table_check_memo">
							<label for="results_<?php echo $criterion; ?>_memo"><?php echo A11YC_LANG_OPINION ?></label>
							<textarea name="results[<?php echo $criterion; ?>][memo]" id="results_<?php echo $criterion; ?>_memo" rows="3"><?php echo Util::s(Arr::get($results, "{$criterion}.memo")); ?></textarea>
						</td>
						<td class="a11yc_table_check_user">
							<select name="results[<?php echo $criterion ?>][uid]">
					<?php
					foreach ($users as $uid => $name):
						$selected = Arr::get($results, "{$criterion}.uid") == $uid ? ' selected="selected"' : '';
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
				<a href="<?php echo A11YC_ISSUES_ADD_URL.Util::urlenc($url).'&amp;criterion='.$criterion ?>" target="_blank"><?php echo A11YC_LANG_ISSUES_ADD ?></a>

			<?php
			if ($issues[$criterion]):
				echo '<ul>';
				foreach ($issues[$criterion] as $issue):
					echo '<li><a href="'.A11YC_ISSUES_VIEW_URL.intval($issue['id']).'" target="_blank">'.Util::s($issue['id'].': '.$issue['error_message']).' ['.$statuses[$issue['status']].']</a></li>';
				endforeach;
				echo '</ul>';
			endif;
			?>

			</div>
			<!-- /.a11yc_issues -->


			<?php
			// main check form
			foreach (array('t', 'f') as $tf):
			if ( ! isset($yml['techs_codes'][$criterion][$tf])) continue;
			?>
			<!-- checks <?php echo $tf ?> -->
			<table class="a11yc_table_check"><tbody>
			<?php
			$i = 0;

			$type = Arr::get($page, 'type') == 2 ? 'pdf' : 'html';

			foreach ($yml['techs_codes'][$criterion][$tf] as $tcode):
				$fcnt++;
				$class_str = ++$i%2==0 ? ' class="even"' : ' class="odd"';
				$id = $criterion.'_'.$tcode;
				$data = ' data-pass="'.$tcode.'"';

				$checked = isset($cs[$tcode]) ? ' checked="checked"' : '';

				if ($type == 'html' && $yml['techs'][$tcode]['type'] == 'PDF') continue;
				if ($type == 'pdf'  && ! in_array($yml['techs'][$tcode]['type'], array('PDF'))) continue;
			?>

				<tr<?php echo $class_str ?>>
					<th scope="row">
					<label for="<?php echo $id ?>"><input type="checkbox"<?php echo $checked ?> id="<?php echo $id ?>" name="chk[<?php echo $tcode ?>]" value="1" <?php echo $data ?> class="<?php echo strtolower($vvv['level']['name']) ?> a11yc_skip"/><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span><?php echo $yml['techs'][$tcode]['title'] ?></label>
					</th>

					<td class="a11yc_table_check_howto">
					<a<?php echo A11YC_TARGET ?> href="<?php echo $refs['t'].$tcode ?>.html" title="<?php echo A11YC_LANG_DOCS_TITLE ?>" class="a11yc_link_howto"><span role="presentation" aria-hidden="true" class="a11yc_icon_fa a11yc_icon_howto"></span><span class="a11yc_skip"><?php echo A11YC_LANG_DOCS_TITLE ?></span></a>
					</td>
				</tr>
			<?php
				endforeach;
			?>
			</tbody></table>
			<!-- /checks <?php echo $tf ?> -->
			<?php
				endforeach;
				// echo $fcnt;
			?>


			</div><!--/#c_<?php echo $criterion ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?>.section_guidelines-->
<?php endforeach; ?>

<input type="hidden" name="page_title" value="<?php echo Util::s($target_title) ?>" />
<input type="hidden" name="url" value="<?php echo Util::s($url) ?>" />

</div><!-- /#a11yc_checks -->
