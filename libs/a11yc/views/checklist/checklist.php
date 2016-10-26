<?php if ($url == 'bulk'): ?>
	<form action="<?php echo A11YC_BULK_URL ?>" method="POST" id="a11yc_form_checklist">
<?php else: ?>
	<form action="<?php echo A11YC_CHECKLIST_URL ?>&amp;url=<?php echo urlencode($url) ?>" method="POST" id="a11yc_form_checklist">
<?php endif; ?>

<?php echo $form ?>
	<div id="a11y_submit">
<?php
	// is done
	if ($url != 'bulk'):
	$checked = @$page['done'] ? ' checked="checked"' : '';
?>
	<!-- is do_link_check -->
	<label for="a11yc_do_link_check"><input type="checkbox" name="do_link_check" id="a11yc_do_link_check" value="1" /><?php echo A11YC_LANG_CHECKLIST_DO_LINK_CHECK ?></label>

	<!-- is done -->
	<label for="a11yc_done"><input type="checkbox" name="done" id="a11yc_done" value="1"<?php echo $checked ?> /><?php echo A11YC_LANG_CHECKLIST_DONE ?></label>
<?php endif; ?>
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</div>
</form>
