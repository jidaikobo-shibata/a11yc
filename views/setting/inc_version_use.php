<?php namespace A11yc; ?>

<?php if ( ! empty($versions)): ?>
<h2><label for="a11yc_target_version"><?php echo A11YC_LANG_SETTING_VERSION ?></h2>
<p>
<select id="a11yc_target_version" name="target_version">
	<option value="0"><?php echo A11YC_LANG_SETTING_VERSION_CURRENT ?></option>
	<?php
	foreach ($versions as $k => $v):
		$selected = Model\Version::current() == $k ? ' selected="selected"' : '';
		echo '<option value="'.$k.'"'.$selected.'>'.$v['name'].'</option>';
	endforeach;
	?>
</select>
</p>
<?php endif; ?>
