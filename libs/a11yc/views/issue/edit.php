<?php namespace A11yc; ?>

<form action="<?php echo Util::uri() ?>" method="POST" enctype="multipart/form-data">

<?php echo $form ?>

<div id="a11yc_submit">
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>

</form>
