<?php namespace A11yc; ?>

<?php if ( ! empty($sites)): ?>
<h2><label for="site"><?php echo A11YC_LANG_SETTING_SITE_CHANGE ?></label></h2>
<select name="site" id="site">
<?php
foreach ($sites as $id => $site):
$selected = $id == $group_id ? ' selected="selected"' : '';
?>
	<option<?php echo $selected ?> value="<?php echo $id ?>"><?php echo $site ?></option>
<?php endforeach; ?>
</select>
<?php endif; ?>

<h2><label for="new_site"><?php echo A11YC_LANG_CTRL_ADDNEW ?></label></h2>
<p><?php echo A11YC_LANG_SETTING_SITE_ADD_EXP  ?></p>
<input type="text" name="new_site" style="width: 100%;" id="new_site" value="">
