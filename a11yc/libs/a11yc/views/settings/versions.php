<?php namespace A11yc; ?>

<?php echo $submenu ?>

<!-- protect current version -->
<h2><?php echo A11YC_LANG_DISCLOSURE_PROTECT_VERSION_TITLE ?></h2>
<form action="<?php echo Util::uri() ?>" method="POST">
<?php echo $protect_form; ?>
</form>

<?php if ($versions):  ?>
<!-- versions -->
<form action="<?php echo Util::uri() ?>" method="POST">
<?php echo $form; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>
<?php endif; ?>
