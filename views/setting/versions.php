<?php namespace A11yc; ?>

<?php echo $submenu ?>

<!-- protect current version -->
<form action="<?php echo A11YC_SETTING_URL.'versions' ?>" method="POST">
<?php echo $protect_form; ?>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>

<?php if ($versions):  ?>
<!-- versions -->
<form action="<?php echo A11YC_SETTING_URL.'versions' ?>" method="POST">
<?php echo $form; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>
<?php endif; ?>
