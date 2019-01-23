<?php namespace A11yc; ?>
<?php if (Input::post('username') || Input::post('password')): ?>
	<p><strong><?php echo A11YC_LANG_LOGIN_ERROR0 ?></strong></p>
<?php endif; ?>

<form action="<?php echo A11YC_URL ?>" method="POST">
	<label for="A11YC_username"><?php echo A11YC_LANG_LOGIN_USERNAME ?></label>
	<input type="text" name="username" id="A11YC_username" size="20" value="" />

	<label for="A11YC_password"><?php echo A11YC_LANG_LOGIN_PASWWORD ?></label>
	<input type="password" name="password" id="A11YC_password" autocomplete="off" size="20" value="" />
	<input type="submit" value="<?php echo A11YC_LANG_LOGIN_BTN ?>" />
</form>
