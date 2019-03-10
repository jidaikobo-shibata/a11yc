<?php namespace A11yc; ?>

<?php if ($url == 'bulk'): ?>
	<?php include('inc_submenu.php'); ?>

	<form action="<?php echo A11YC_BULK_URL ?>index" method="POST" id="a11yc_form_checklist" class="a11yc_form_confirm">
<?php else: ?>
	<form action="<?php echo A11YC_CHECKLIST_URL ?>&amp;url=<?php echo Util::urlenc($url) ?>" method="POST" id="a11yc_form_checklist" class="a11yc_form_confirm">
<?php
endif;

// see form.php
echo $form
?>
<div id="a11yc_submit">
<?php
	// is done
	if ($url != 'bulk'):
	$checked = Arr::get($page, 'done') ? ' checked="checked"' : '';
?>
	<!-- alternative content url -->
	<label for="a11yc_alt_url"><?php echo A11YC_LANG_CHECKLIST_ALT_URL ?> <input type="text" name="alt_url" id="a11yc_alt_url" value="<?php echo $alt_url ?>" /></label>

	<!-- is do_css_check -->
	<label for="a11yc_do_css_check"><input type="checkbox" name="do_css_check" id="a11yc_do_css_check" value="1" /><?php echo A11YC_LANG_CHECKLIST_DO_CSS_CHECK ?></label>

	<!-- is do_link_check -->
	<label for="a11yc_do_link_check"><input type="checkbox" name="do_link_check" id="a11yc_do_link_check" value="1" /><?php echo A11YC_LANG_CHECKLIST_DO_LINK_CHECK ?></label>

	<!-- is done -->
	<label for="a11yc_done"><input type="checkbox" name="done" id="a11yc_done" value="1"<?php echo $checked ?> /><?php echo A11YC_LANG_CHECKLIST_DONE ?></label>
<?php endif; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>
</form>
