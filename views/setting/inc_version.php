<?php namespace A11yc; ?>
<?php if ( ! empty($versions)): ?>
<h2><?php echo A11YC_LANG_SETTING_TITLE_VERSION ?></h2>
<p><?php echo A11YC_LANG_SETTING_TITLE_VERSION_EXP ?></p>
<table class="a11yc_table">
<thead>
<tr>
	<th><?php echo A11YC_LANG_CTRL_NAME ?></th>
	<th style="text-align: center;"><?php echo A11YC_LANG_CTRL_VIEW ?></th>
	<th><?php echo A11YC_LANG_CTRL_PURGE ?></th>
	<th><?php echo A11YC_LANG_CTRL_DATE ?></th>
</tr>
</thead>

	<tbody>
<?php
foreach ($versions as $version => $v):
$id = intval($version);
$checked = $v['trash'] ? '' : ' checked="checked"';
?>

	<tr>
	<td>
		<label><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_NAME ?></span><input type="text" name="name[<?php echo $id ?>]" style="width: 100%;" id="name_<?php echo $id ?>" value="<?php echo Util::s($v['name']) ?>"></label>
	</td>

	<td style="width:5em; text-align: center;">
		<label><input<?php echo $checked ?> type="checkbox" name="trash[<?php echo $id ?>]" id="trash_<?php echo $id ?>" value="<?php echo $id ?>"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_PURGE.' ('.Util::s($v['name']).')' ?></span></label>
	</td>

	<td style="width:5em; text-align: center;">
		<label><input type="checkbox" name="delete[<?php echo $id ?>]" id="delete_<?php echo $id ?>" value="<?php echo $id ?>"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_VIEW.' ('.Util::s($v['name']).')' ?></span></label>
	</td>

	<td style="width:5em;white-space: nowrap;"><?php echo date('Y-m-d', strtotime($version)) ?></td>
	</tr>

<?php
endforeach;
?>

	</tbody>
</table>
<?php endif; ?>
