<?php if ($errs):
	// error
?><h1 class="a11yc_disclosure a11yc_resetstyle">error_check</h1>
	<div class="a11yc_disclosure_target show">
		<dl id="a11yc_validation_errors" class="a11yc_hide_if_fixedheader">
		<?php foreach ($errs as $err):  ?>
			<?php echo $err ?>
		<?php endforeach;  ?>
		</dl><!-- /.a11yc_validation_errors -->
		<dl id="a11yc_validation_code" class="a11yc_hide_if_fixedheader">
			<dt>
				<a role="button" class="a11yc_disclosure a11yc_resetstyle" tabindex="0"><?php echo A11YC_LANG_CHECKLIST_VIEW_SOURCE ?></a>
			</dt>
			<dd>
				<div class="a11yc_disclosure_target a11yc_source show">
					<?php echo $raw ?>
				</div><!-- /.a11yc_disclosure_target -->
			</dd>
		</dl>
</div><!-- /.a11yc_disclosure_target -->
<?php else:
	echo '<p class="a11yc_hide_if_fixedheader">'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
<!-- /#a11yc_errors -->
