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


<h2><?php echo A11YC_LANG_SETTING_SITE_CHANGE_URL ?></h2>

<table class="a11yc_table">
<tr>
	<th><label for="change_url_target"><?php echo A11YC_LANG_SETTING_SITE_CHANGE_URL_TARGET ?></label></th>
	<td>
	<select name="change_url_target" id="change_url_target">
		<option value="">-</option>
	<?php foreach ($sites as $id => $site): ?>
		<option value="<?php echo $id ?>"><?php echo $site ?></option>
	<?php endforeach; ?>
	</select>
</td>
</tr>

<tr>
	<th><label for="change_url_new_url"><?php echo A11YC_LANG_SETTING_SITE_CHANGE_URL_NEW ?></label>	<td><input type="text" name="change_url_new_url" id="change_url_new_url" style="width: 100%;" value=""></td>
</th>
</table>
<p><label><input type="checkbox" name="change_url_confirm" value="1" data-a11yc-confirm="Confirm <?php echo A11YC_LANG_SETTING_SITE_CHANGE_URL_CHECK ?>"> <?php echo A11YC_LANG_SETTING_SITE_CHANGE_URL_CHECK ?></label></p>

<?php endif; ?>

<h2><label for="new_site"><?php echo A11YC_LANG_CTRL_ADDNEW ?></label></h2>
<p><?php echo A11YC_LANG_SETTING_SITE_ADD_EXP  ?></p>
<input type="text" name="new_site" style="width: 100%;" id="new_site" value="">
