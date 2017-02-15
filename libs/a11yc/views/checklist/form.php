<div id="a11yc_header_ctrl">
<?php if ($url != 'bulk'): ?>
	<!-- standard -->
	<p id="a11yc_done_date" class="a11yc_hide_if_fixedheader">
		<label for="a11yc_done_date_val"><?php echo A11YC_LANG_TEST_DATE ?></label>
		<input type="text" name="done_date" id="a11yc_done_date_val" size="10" value="<?php echo $done_date ?>" />
	</p>

	<!-- standard -->
	<p id="a11yc_select_standard" class="a11yc_hide_if_fixedheader">
		<label for="a11yc_standard_val"><?php echo A11YC_LANG_STANDARD ?></label>
		<select name="standard" id="a11yc_standard_val">
		<?php
		foreach ($standards as $k => $v):
			$selected = $k == @$page['standard'] ? ' selected="selected"' : '';
		?>
			<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
		<?php endforeach; ?>
		</select>
	</p>

	<!-- selection reason -->
	<p id="a11yc_selection_reason" class="a11yc_hide_if_fixedheader">
		<label for="a11yc_selection_reason_val"><?php echo A11YC_LANG_CANDIDATES_REASON ?></label>
		<select name="selection_reason" id="a11yc_selection_reason_val">
		<?php
		foreach ($selection_reasons as $k => $v):
			$selected = $k == @$page['selection_reason'] ? ' selected="selected"' : '';
		?>
			<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
		<?php endforeach; ?>
		</select>
	</p>
<?php endif; ?>

<!-- narrow level -->
	<p class="a11yc_narrow_level a11yc_hide_if_no_js" data-a11yc-narrow-target=".a11yc_section_principle">Level:
<?php
	for ($i=1; $i<=3; $i++)
	{
		$class_str = ($i == $target_level) || ($target_level == 0 && $i == 3) ? ' class="current"' : '';
		echo '<a role="button" tabindex="0" data-narrow-level="'.implode(',', array_slice(array('l_a', 'l_aa', 'l_aaa'), 0, $i)).'"'.$class_str.'>'.\A11yc\Util::num2str($i).'</a>';
	}
	$additional_criterion = join(',', \A11yc\Controller_Setup::additional_criterions());
?>
	</p>
</div><!-- /#a11yc_header_ctrl -->
<div id="a11yc_checks" data-a11yc-additional_criterions="<?php echo $additional_criterion ?>" data-a11yc-current-user="<?php echo $current_user_id ?>" data-a11yc-lang='{"expand":"<?php echo A11YC_LANG_CTRL_EXPAND ?>", "compress": "<?php echo A11YC_LANG_CTRL_COMPRESS ?>", "conformance": "<?php echo A11YC_LANG_CHECKLIST_CONFORMANCE.','.A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL ?>"}' <?php if($checklist_behaviour) echo ' class="a11yc_hide_passed_item"' ?>>
<!-- header -->
<div id="a11yc_header">
	<div id="a11yc_header_left" class="a11yc_fl">
		<!-- not for bulk -->
	<?php if ($url != 'bulk'): ?>
		<div id="a11yc_targetpage_data">
		<!-- target page -->
	<table id="a11yc_back_to_target_page">
	<tr>
		<th><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
		<td><?php echo $target_title ?></td>
	</tr>
	<tr>
		<th><?php echo A11YC_LANG_PAGES_URLS ?></th>
		<td><a href="<?php echo \A11yc\Util::urldec($url) ?>"><?php echo \A11yc\Util::urldec($url) ?></a></td>
	</tr>
	</table>

	<?php
		// #a11yc_error
		echo $ajax_validation;
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
	<?php if ($url != 'bulk'): ?>
		<!-- level -->
		<p id="a11yc_target_level"><?php echo A11YC_LANG_TARGET_LEVEL ?>: <?php echo \A11yc\Util::num2str($target_level) ?>
<?php $current_level = $target_level ? \A11yc\Evaluate::result_str(@$page['level'], $target_level) : '-'; ?><br><?php echo A11YC_LANG_CURRENT_LEVEL ?>: <span id="a11yc_conformance_level"><?php echo $current_level ?></span></p>
	<?php endif ?>
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
		<?php /*
			$checked = $setup['checklist_behaviour'] ? ' checked="checked"' : '';
		?>
		<label for="a11yc_checklist_behaviour" class="a11yc_label_switch"><span role="presentation" aria-hidden="true"></span><input type="checkbox" name="checklist_behaviour" id="a11yc_checklist_behaviour" value=""<?php echo $checked ?> class="" /><?php echo A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR ?></label>
		<?php */ ?>
	</div><!-- /#a11yc_header_right -->

		<!-- a11yc menu -->
	<ul id="a11yc_menu_principles">
	<?php foreach ($yml['principles'] as $v): ?>
		<li id="a11yc_menuitem_<?php echo $v['code'] ?>"><a href="#a11yc_header_p_<?php echo $v['code'] ?>"><?php echo $v['code'].' '.$v['name'] ?></a></li>
	<?php endforeach; ?>
	</ul><!--/#a11yc_menu_principles-->

</div><!--/#a11yc_header-->

<?php foreach ($yml['principles'] as $k => $v): ?>
	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue;
	?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><h3 class="a11yc_header_guideline"><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<?php
		foreach ($yml['criterions'] as $kkk => $vvv):
			if (substr($kkk, 0, 3) != $kk) continue;
			$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
			$skip_non_interference = isset($vvv['non-interference']) ? '<span class="a11yc_skip">&nbsp;('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</span>' : '';
			$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
			$class_str.= ' a11yc_level_'.strtolower($vvv['level']['name']);
			$class_str.= ' a11yc_p_'.$k.'_criterion';
		?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion<?php echo $class_str ?>">
			<h4 class="a11yc_header_criterion"><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' <span class="a11yc_header_criterion_level">('.$vvv['level']['name'].$non_interference.')</span>' ?></h4>
			<ul class="a11yc_outlink">
			<?php if (isset($vvv['url_as'])): ?>
				<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
			<?php endif; ?>
				<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
			</ul>
			<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>

			<!-- .a11yc_ng NG: NONCONFORMITY -->
			<div class="a11yc_ng">
				<?php
					$ng_id = 'a11yc_ng_'.$kkk;
				?>
				<h5><label for="<?php echo $ng_id; ?>"><?php echo A11YC_LANG_CHECKLIST_NG_REASON; ?></label></h5>
				<p><?php echo A11YC_LANG_CHECKLIST_NG_REASON_EXP; ?></p>
				<textarea name="ngs[<?php echo $kkk; ?>][memo]" id="<?php echo $ng_id; ?>" rows="3"><?php
				$memo = $is_new ? \A11yc\Arr::get($bulk_ngs, "{$kkk}.memo") : \A11yc\Arr::get($cs_ngs, "{$kkk}.memo") ;
					echo $memo;
				?></textarea>

				<select name="ngs[<?php echo $kkk ?>][uid]">
				<?php
				foreach ($users as $uid => $name):
					$selected = '';
					if (
						(isset($cs_ngs[$kkk]['uid']) && (int) $cs_ngs[$kkk]['uid'] == $uid) ||
						(isset($bulk_ngs[$kkk]['uid']) && (int) $bulk_ngs[$kkk]['uid'] == $uid)
					):
						$selected = ' selected="selected"';
					endif;
				?>
					<option value="<?php echo $uid ?>"<?php echo $selected ?>><?php echo $name ?></option>
				<?php endforeach; ?>
				</select>
			</div>
			<!-- /.a11yc_ng NG: NONCONFORMITY -->

			<!-- checks -->
			<table class="a11yc_table_check"><tbody>
			<?php
			$i = 0;
			foreach ($yml['checks'][$kkk] as $code => $val):
				$class_str = ++$i%2==0 ? ' class="even"' : ' class="odd"';
				$passes = array();
				if (isset($val['pass'])):
					foreach ($val['pass'] as $pass_code => $pass_each):
						$passes = array_merge($passes, $pass_each);
					endforeach;
				endif;
				$data = $passes ? ' data-pass="'.join(',', $passes).'"' : '';
				$data.= isset($val["non-exist"]) ? ' data-non-exist="'.join(',', $val["non-exist"]).'"' : '';
				$checked = '';

				if (
					($page && isset($cs[$code]) && $cs[$code]['passed']) || // ordinary
					($page && isset($bulk[$code]) && empty($cs)) || // default
					( ! $page && isset($bulk[$code])) // bulk
				):
					$checked = ' checked="checked"';
				endif;
			?>

				<tr<?php echo $class_str ?>>
				<th scope="row">
				<label for="<?php echo $code ?>"><input type="checkbox"<?php echo $checked ?> id="<?php echo $code ?>" name="chk[<?php echo $code ?>][on]" value="1" <?php echo $data ?> class="<?php echo $vvv['level']['name'] ?> a11yc_skip"/><span class="a11yc_icon_fa a11yc_icon_checkbox" role="presentation" aria-hidden="true"></span><?php echo $skip_non_interference.$val['name'] ?></label>
				</th>

				<td class="a11yc_table_check_memo">
				<?php $memo = isset($cs[$code]['memo']) ? $cs[$code]['memo'] : @$bulk[$code]['memo'] ; ?>
				<textarea name="chk[<?php echo $code ?>][memo]"><?php echo $memo ?></textarea>
				</td>

				<td class="a11yc_table_check_user">
				<select name="chk[<?php echo $code ?>][uid]">
				<?php
				foreach ($users as $uid => $name):
					$selected = '';
					if (
						(isset($cs[$code]['uid']) && (int) $cs[$code]['uid'] == $uid) ||
						(isset($bulk[$code]['uid']) && (int) $bulk[$code]['uid'] == $uid)
					):
						$selected = ' selected="selected"';
					endif;
				?>
					<option value="<?php echo $uid ?>"<?php echo $selected ?>><?php echo $name ?></option>
				<?php endforeach; ?>
				</select>
				</td>
				<td class="a11yc_table_check_howto">
				<a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>" title="<?php echo A11YC_LANG_DOCS_TITLE ?>" class="a11yc_link_howto"><span role="presentation" aria-hidden="true" class="a11yc_icon_fa a11yc_icon_howto"></span><span class="a11yc_skip"><?php echo A11YC_LANG_DOCS_TITLE ?></span></a>
				</td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?>.section_guidelines-->
<?php endforeach; ?>
<input type="hidden" name="page_title" value="<?php echo s($target_title) ?>" />
<input type="hidden" name="url" value="<?php echo s($url) ?>" />
</div><!-- /#a11yc_checks -->
