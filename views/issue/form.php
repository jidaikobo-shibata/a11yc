<?php namespace A11yc; ?>

<table class="a11yc_table a11yc_issues">

<tr>
	<th><label for="a11yc_title"><?php echo A11YC_LANG_ISSUE_TITLE_EACH ?></label></th>
	<td>
		<input type="text" id="a11yc_title" name="title" style="width: 100%;" value="<?php echo Arr::get($item, 'title', '') ?>" />
	</td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_CTRL_VIEW ?></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUE_EXPORT_EXP ?></p>
		<?php $checked = Arr::get($item, 'output', true) ? ' checked="checked"' : ''; ?>
		<label><input<?php echo $checked ?> type="checkbox" id="a11yc_output" name="output" value="1" /><?php echo A11YC_LANG_CTRL_VIEW ?></label>
	</td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUE_IS_COMMON ?></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUE_IS_COMMON_EXP ?></p>
		<?php $checked = Arr::get($item, 'is_common', false) ? ' checked="checked"' : ''; ?>
		<label><input<?php echo $checked ?> type="checkbox" id="a11yc_is_common" name="is_common" value="1" /><?php echo A11YC_LANG_ISSUE_IS_COMMON ?></label>
	</td>
</tr>

<tr>
	<th><label for="a11yc_url">URL</label></th>
	<td>
		<input type="text" id="a11yc_url" name="url" style="width: 100%;" value="<?php echo Arr::get($item, 'url', '') ?>" />
	</td>
</tr>

<tr>
	<th><label for="a11yc_n_or_e"><?php echo A11YC_LANG_ISSUE_N_OR_E ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUE_N_OR_E_EXP ?></p>
		<select name="n_or_e" id="a11yc_n_or_e">
			<?php $selected = Arr::get($item, 'n_or_e', 0) == 0 ? ' selected="selected"': ''; ?>
			<option<?php echo $selected ?> value="0">Notice</option>
			<?php $selected = Arr::get($item, 'n_or_e', 0) == 1 ? ' selected="selected"': ''; ?>
			<option<?php echo $selected ?> value="1">Error</option>
		</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_html"><?php echo A11YC_LANG_ISSUE_HTML ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUE_HTML_EXP ?></p>
		<textarea name="html" id="a11yc_html" cols="35" rows="7"><?php echo Arr::get($item, 'html', '') ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_error_message"><?php echo A11YC_LANG_ISSUE_ERRMSG ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUE_ERRMSG_EXP ?></p>
		<textarea name="error_message" id="a11yc_error_message" cols="35" rows="7"><?php echo Arr::get($item, 'error_message', '') ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_uid"><?php echo A11YC_LANG_CTRL_PERSONS ?></label></th>
	<td>
	<select name="uid" id="a11yc_uid">
		<?php
		foreach ($users as $each_uid => $user):
			$selected = $each_uid == Arr::get($item, 'uid', 0) ? ' selected="selected"': '';
			echo '<option'.$selected.' value="'.$each_uid.'">'.$user.'</option>';
		endforeach;
		?>
	</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_status"><?php echo A11YC_LANG_ISSUE_STATUS ?></label></th>
	<td>
	<select name="status" id="a11yc_status">
		<?php $selected = Arr::get($item, 'status', 0) == 0 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="0"><?php echo A11YC_LANG_ISSUE_STATUS_1 ?></option>
		<?php $selected = Arr::get($item, 'status', 0) == 1 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="1"><?php echo A11YC_LANG_ISSUE_STATUS_2 ?></option>
		<?php $selected = Arr::get($item, 'status', 0) == 2 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="2"><?php echo A11YC_LANG_ISSUE_STATUS_3 ?></option>
	</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_file_path"><?php echo A11YC_LANG_ISSUE_SCREENSHOT ?></label></th>
	<td>
		<input id="a11yc_file_path" type="text" name="file_path" value="<?php echo Arr::get($item, 'image_path', '') ?>"/>
		<input type="file" name="file" value=""/>
		<?php
		if (Arr::get($item, 'image_path', false)):
			echo '<div><img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/issues/'.$item['image_path'].'" alt="" /></div>';
		endif;
		?>
	</td>
</tr>

<tr>
	<th><label for="a11yc_other_urls"><?php echo A11YC_LANG_ISSUE_OTHER_URLS ?></label></th>
	<td>
		<textarea name="other_urls" id="a11yc_other_urls" cols="35" rows="7"><?php echo Arr::get($item, 'other_urls', '') ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_memo"><?php echo A11YC_LANG_ISSUE_MEMO ?></label></th>
	<td>
		<textarea name="memo" id="a11yc_memo" cols="35" rows="7"><?php echo Arr::get($item, 'memo', '') ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_seq"><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></label></th>
	<td>
		<input type="text" id="a11yc_seq" name="seq" style="width: 5em;" value="<?php echo intval(Arr::get($item, 'seq', 0)) ?>" />
	</td>
</tr>

<?php echo View::fetchTpl('inc_implements_checkbox.php') ?>

<?php include('inc_pages.php'); ?>

</table>
