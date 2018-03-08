<?php namespace A11yc; ?>

<form action="<?php echo Util::uri() ?>" method="POST">
<table class="a11yc_table">

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

</table>

<div id="a11yc_submit">
	<a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></a>
	<?php if ( ! $is_new): ?>
	<a href="<?php echo A11YC_ISSUES_VIEW_URL.intval($issue_id) ?>"><?php echo A11YC_LANG_ISSUES_TITLE ?></a>
	<select name="is_delete">
		<option value="0"></option>
		<option value="1"><?php echo A11YC_LANG_PAGES_PURGE; ?></option>
	</select>
	<?php endif; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>

</form>
