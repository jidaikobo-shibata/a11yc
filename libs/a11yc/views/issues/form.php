<?php namespace A11yc; ?>

<table class="a11yc_table a11yc_issues">

<tr>
	<th><?php echo A11YC_LANG_ISSUES_TITLE_EACH ?></th>
	<td>
		<label><input type="text" name="title" style="width: 100%;" value="<?php echo $issue_title ?>" /></label>
	</td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUES_IS_COMMON_EXP ?></p>
		<?php $checked = $is_common ? ' checked="checked"' : ''; ?>
		<label><input<?php echo $checked ?> type="checkbox" name="is_common" value="1" /><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></label>
	</td>
</tr>

<tr>
	<th><label for="a11yc_n_or_e"><?php echo A11YC_LANG_ISSUES_N_OR_E ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUES_N_OR_E_EXP ?></p>
		<select name="n_or_e" id="a11yc_n_or_e">
			<?php $selected = $n_or_e == 0 ? ' selected="selected"': ''; ?>
			<option<?php echo $selected ?> value="0">Notice</option>
			<?php $selected = $n_or_e == 1 ? ' selected="selected"': ''; ?>
			<option<?php echo $selected ?> value="1">Error</option>
		</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_html"><?php echo A11YC_LANG_ISSUES_HTML ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUES_HTML_EXP ?></p>
		<textarea name="html" id="a11yc_html" cols="35" rows="7"><?php echo $html ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_error_message"><?php echo A11YC_LANG_ISSUES_ERRMSG ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUES_ERRMSG_EXP ?></p>
		<textarea name="error_message" id="a11yc_error_message" cols="35" rows="7"><?php echo $error_message ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_uid"><?php echo A11YC_LANG_CTRL_PERSONS ?></label></th>
	<td>
	<select name="uid" id="a11yc_uid">
		<?php
		foreach ($users as $each_uid => $user):
			$selected = $each_uid == $uid ? ' selected="selected"': '';
			echo '<option'.$selected.' value="'.$each_uid.'">'.$user.'</option>';
		endforeach;
		?>
	</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_status"><?php echo A11YC_LANG_ISSUES_STATUS ?></label></th>
	<td>
	<select name="status" id="a11yc_status">
		<?php $selected = $status == 0 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="0"><?php echo A11YC_LANG_ISSUES_STATUS_1 ?></option>
		<?php $selected = $status == 1 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="1"><?php echo A11YC_LANG_ISSUES_STATUS_2 ?></option>
		<?php $selected = $status == 2 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="2"><?php echo A11YC_LANG_ISSUES_STATUS_3 ?></option>
	</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_tech_url"><?php echo A11YC_LANG_ISSUES_TECH ?></label></th>
	<td>
		<p><?php echo A11YC_LANG_ISSUES_TECH_EXP ?></p>
		<textarea name="tech_url" id="a11yc_tech_url" cols="35" rows="7"><?php echo $tech_url ?></textarea>
	</td>
</tr>

<tr>
	<th><label for="a11yc_tech_url"><?php echo A11YC_LANG_ISSUES_SCREENSHOT ?></label></th>
	<td>
		<input type="text" name="file_path" value="<?php echo Util::s($image_path) ?>"/>
		<input type="file" name="file" value=""/>
		<?php
		if ($image_path):
			echo '<div><img src="'.dirname(A11YC_URL).'/screenshots/issues/'.$issue_id.'/'.$image_path.'" alt="" /></div>';
		endif;
		?>
	</td>
</tr>

</table>
