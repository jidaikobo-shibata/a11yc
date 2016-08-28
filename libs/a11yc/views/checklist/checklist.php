<?php if ($url == 'bulk'): ?>
	<form action="<?php echo A11YC_BULK_URL ?>" method="POST">
<?php else: ?>
	<form action="<?php echo A11YC_CHECKLIST_URL.$url ?>" method="POST">
<?php endif; ?>
<?php echo $form ?>
	<div id="a11y_submit">
		<input type="submit" value="<?php echo A11YC_LANG_SETUP_SUBMIT ?>" />
	</div>
</form>
