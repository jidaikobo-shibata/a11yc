<?php if (isset($_POST['username'])):  ?>
  <p><strong><?php echo A11YC_LANG_LOGIN_ERROR0 ?></strong></p>
<?php endif; ?>

<form action="" method="POST">
<label for="a11yc_username"><?php echo A11YC_LANG_LOGIN_USERNAME ?></label>
<input type="text" name="username" id="a11yc_username" size="20" value="" />

<label for="a11yc_password"><?php echo A11YC_LANG_LOGIN_PASWWORD ?></label>
<input type="password" name="password" id="a11yc_password" size="20" value="" />
<input type="submit" value="<?php echo A11YC_LANG_LOGIN_BTN ?>" />
</form>
