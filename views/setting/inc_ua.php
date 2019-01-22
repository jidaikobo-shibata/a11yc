<?php namespace A11yc; ?>
<h2><?php echo A11YC_LANG_SETTING_TITLE_UA ?></h2>
<p><?php echo A11YC_LANG_SETTING_TITLE_UA_EXP ?></p>
<table class="a11yc_table">
<thead>
<tr>
	<th><?php echo A11YC_LANG_CHECKLIST_UA ?></th>
	<th><?php echo A11YC_LANG_VALUE ?></th>
	<th><?php echo A11YC_LANG_CTRL_PURGE ?></th>
</tr>
</thead>

<?php
foreach ($uas as $ua):
$id = intval($ua['id']);
$disabled = $id == 1 ? ' disabled="disabled"' : '';
?>
	<tbody>

	<tr>
	<td style="width:12em;">
		<label><span class="a11yc_skip"><?php echo A11YC_LANG_CHECKLIST_UA ?></span><input type="text" name="name[<?php echo $id ?>]" style="width: 100%;" id="name_<?php echo $id ?>" value="<?php echo Util::s($ua['name']) ?>"></label>
	</td>
	<td>
		<label><span class="a11yc_skip"><?php echo A11YC_LANG_VALUE ?></span><input type="text" name="str[<?php echo $id ?>]" style="width: 100%;" id="str_<?php echo $id ?>" value="<?php echo Util::s($ua['str']) ?>"></label>
	</td>
	<td style="width:5em; text-align: center;">
		<label><input<?php echo $disabled ?> type="checkbox" name="delete[<?php echo $id ?>]" id="delete_<?php echo $id ?>" value="<?php echo $id ?>"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_PURGE.' ('.Util::s($ua['name']).')' ?></span></label>
	</td>
	</tr>

<?php
endforeach;
?>

	<tr>
	<td style="width:12em;">
		<input type="text" name="new_name" style="width: 100%;" id="new_name" value="">
	</td>
	<td colspan="2">
		<input type="text" name="new_str" style="width: 100%;" id="new_str" value="">
	</td>
	</tr>

	</tbody>
</table>
