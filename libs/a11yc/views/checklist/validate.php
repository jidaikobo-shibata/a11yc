<?php
// call from post
$is_call_from_post = isset($is_call_from_post);

if ($errs):
// error
	$class_str = $is_call_from_post ? '' : ' a11yc_disclosure';
?><h1 class="a11yc_resetstyle a11yc_narrow_level<?php echo $class_str ?>" data-a11yc-narrow-target="#a11yc_validation_list"><?php echo $is_call_from_post ? A11YC_LANG_CHECKLIST_CHECK_RESULT : A11YC_LANG_CHECKLIST_MACHINE_CHECK ?>
	<?php
		foreach ($errs_cnts as $lv => $errs_cnt):
			$narrow_level = $lv=='total' ? '"l_a","l_aa","l_aaa"' : '"l_'.$lv.'"';
			$class_str = $lv == 'total' ? ' current' : '';
			echo '<a role="button" class="a11yc_resetstyle'.$class_str.'" tabindex="0" data-narrow-level=\'['.$narrow_level.']\'>'.strtoupper($lv).': '.intval($errs_cnt).'</a> ';
		endforeach;

		$class_str = $is_call_from_post ? '' : 'a11yc_disclosure_target a11yc_hide_if_fixedheader hide';
	?>
	</h1>
	<div class="<?php echo $class_str ?>">
		<div id="a11yc_validation_errors" class="">
			<div class="a11yc_controller">
			</div>
			<dl id="a11yc_validation_list">
			<?php foreach ($errs as $err): ?>
				<?php echo $err ?>
			<?php endforeach; ?>
			</dl>
		</div><!-- /#a11yc_validation_errors -->
		<dl id="a11yc_validation_code">
			<dt>
<?php
			$class_str = $is_call_from_post ? '' : ' a11yc_disclosure';
?>
				<a role="button" class="a11yc_resetstyle<?php echo $class_str ?>" tabindex="0"><?php echo A11YC_LANG_CHECKLIST_SOURCE ?></a>
			</dt>
			<dd class="a11yc_disclosure_target show">
				<div class="a11yc_controller"></div>
				<div class="a11yc_source">
					<?php if (strpos($raw, '===a11yc_rplc===') !== false):
						echo A11YC_LANG_CHECKLIST_COULD_NOT_DRAW_HTML;
					else: ?>
					<div id="a11yc_validation_code_raw">
						<?php echo $raw ?>
					</div>
					<?php /* ?>
					<div id="a11yc_validation_code_txt" style="display: none;">
						<?php echo $raw ?>
					</div>
					<?php */ ?>
					<?php endif; ?>
				</div><!-- /.a11yc_disclosure_target -->
			</dd>
		</dl>
</div><!-- /.a11yc_disclosure_target -->
<?php else:
	echo '<p class="">'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
<!-- /#a11yc_errors -->
