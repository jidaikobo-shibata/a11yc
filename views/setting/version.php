<?php namespace A11yc; ?>

<?php include('inc_submenu.php'); ?>

<!-- protect current version -->
<form action="<?php echo A11YC_SETTING_URL.'version' ?>" method="POST">
<?php echo $protect_form; ?>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>

<?php if ($versions):  ?>
<!-- versions -->
<form action="<?php echo A11YC_SETTING_URL.'version' ?>" method="POST">
<?php echo $form; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>
<?php endif; ?>
