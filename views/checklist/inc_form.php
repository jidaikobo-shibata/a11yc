<?php
namespace A11yc;

$selection_reasons     = Values::filteredSelectionReasons();
$additional_criterions = join('","', Model\Setting::fetch('additional_criterions'));
?>

<div id="a11yc_checks" data-a11yc-target_level="<?php echo str_repeat('a',$target_level) ?>" data-a11yc-additional_criterions='[<?php echo $additional_criterions ? '"'.$additional_criterions.'"' : ''?>]' data-a11yc-current-user="<?php echo $current_user_id ?>" data-a11yc-lang='{"expand":"<?php echo A11YC_LANG_CTRL_EXPAND ?>", "compress": "<?php echo A11YC_LANG_CTRL_COMPRESS ?>", "conformance": "<?php echo A11YC_LANG_CHECKLIST_CONFORMANCE.','.A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL ?>"}'>

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
				<td>
				<?php
//				<td title="< ?php echo Util::s(Util::urldec($url)) ? >">
					echo '<a href="'.Util::s(Util::urldec($url)).'">'.Util::s(Util::urldec($url)).'</a> ';
					echo A11YC_LANG_LAST_UPDATE.Model\Html::lastUpdate($url).' ';
					echo '<a href="'.A11YC_PAGE_URL.'updatehtml&amp;url='.Util::s(Util::urlenc($url)).'">'.A11YC_LANG_REFRESH_HTML.'</a>';
				?></td>
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
			include('inc_criterion_form.php');
		endforeach;
		?>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<input type="hidden" name="page_title" value="<?php echo Util::s($target_title) ?>" />
<input type="hidden" name="url" value="<?php echo Util::s($url) ?>" />

</div><!-- /#a11yc_checks -->
