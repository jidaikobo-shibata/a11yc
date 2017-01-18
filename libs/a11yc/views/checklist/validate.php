<?php if ($errs):
	// error
?><h1 class="a11yc_disclosure a11yc_resetstyle a11yc_narrow_level" data-a11yc-narrow-target="#a11yc_validation_list"><?php echo A11YC_LANG_CHECKLIST_MACHINE_CHECK ?>
	<?php
		foreach ($errs_cnts as $lv => $errs_cnt):
			$narrow_level = $lv=='total' ? "l_a,l_aa,l_aaa" : "l_".$lv;
			$class_str = $lv == 'total' ? ' current' : '';
			echo '<a role="button" class="a11yc_resetstyle'.$class_str.'" tabindex="0" data-narrow-level="'.$narrow_level.'">'.strtoupper($lv).': '.intval($errs_cnt).'</a> ';
		endforeach;
	?>
	</h1>
	<div class="a11yc_disclosure_target show">
		<div id="a11yc_validation_errors" class="a11yc_hide_if_fixedheader">
			<div class="a11yc_controller">
			</div>
			<dl id="a11yc_validation_list">
			<?php foreach ($errs as $err): ?>
				<?php echo $err ?>
			<?php endforeach; ?>
			</dl>
		</div><!-- /#a11yc_validation_errors -->
		<dl id="a11yc_validation_code" class="a11yc_hide_if_fixedheader">
			<dt>
				<a role="button" class="a11yc_disclosure a11yc_resetstyle" tabindex="0"><?php echo A11YC_LANG_CHECKLIST_VIEW_SOURCE ?></a>
			</dt>
			<dd class="a11yc_disclosure_target show">
				<div class="a11yc_controller"></div>
				<div class="a11yc_source">
					<div id="a11yc_validation_code_raw">
						<?php echo $raw ?>
					</div>
					<div id="a11yc_validation_code_txt" style="display: none;">
						<?php echo $raw ?>
					</div>
				</div><!-- /.a11yc_disclosure_target -->
			</dd>
		</dl>
</div><!-- /.a11yc_disclosure_target -->
<?php else:
	echo '<p class="a11yc_hide_if_fixedheader">'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
<!-- /#a11yc_errors -->
