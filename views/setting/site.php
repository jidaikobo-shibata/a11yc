<?php namespace A11yc; ?>

<?php include('inc_submenu.php'); ?>

<!-- versions -->
<form action="<?php echo A11YC_SETTING_URL.'site' ?>" method="POST">
<?php echo $form; ?>
	<div id="a11yc_submit">
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</div>
</form>
