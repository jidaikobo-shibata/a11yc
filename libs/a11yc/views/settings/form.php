<?php namespace A11yc;?>

<?php echo $submenu ?>

<!-- setting -->
<form action="<?php echo Util::uri() ?>" method="POST">
<?php echo $form; ?>
	<div id="a11yc_submit">
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</div>
</form>
