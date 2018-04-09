<?php namespace A11yc; ?>

<table class="a11yc_table">

<?php if ($issue['is_common']): ?>
<tr>
	<th><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></th>
	<td><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></td>
</tr>
<?php endif; ?>

<tr>
	<th><label for="a11yc_n_or_e"><?php echo A11YC_LANG_ISSUES_N_OR_E ?></label></th>
	<td>
	<?php
	if ($issue['n_or_e'] == 0):
		echo 'Notice';
	else:
		echo 'Error';
	endif;
	?>
	</td>
</tr>

<tr>
	<th><label for="a11yc_html"><?php echo A11YC_LANG_ISSUES_HTML ?></label></th>
	<td><?php echo $issue['html'] ?></td>
</tr>

<tr>
	<th><label for="a11yc_error_message"><?php echo A11YC_LANG_ISSUES_ERRMSG ?></label></th>
	<td><?php echo $issue['error_message'] ?></td>
</tr>

<tr>
	<th><label for="a11yc_uid"><?php echo A11YC_LANG_CTRL_PERSONS ?></label></th>
	<td><?php echo Arr::get($users, $issue['uid']) ?></td>
</tr>

<tr>
	<th><label for="a11yc_status"><?php echo A11YC_LANG_ISSUES_STATUS ?></label></th>
	<td>
	<select name="status" id="a11yc_status">
		<?php $selected = $issue['status'] == 0 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="0"><?php echo A11YC_LANG_ISSUES_STATUS_1 ?></option>
		<?php $selected = $issue['status'] == 1 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="1"><?php echo A11YC_LANG_ISSUES_STATUS_2 ?></option>
		<?php $selected = $issue['status'] == 2 ? ' selected="selected"': ''; ?>
		<option<?php echo $selected ?> value="2"><?php echo A11YC_LANG_ISSUES_STATUS_3 ?></option>
	</select>
	</td>
</tr>

<?php if ($issue['tech_url']): ?>
<tr>
	<th><label for="a11yc_tech_url"><?php echo A11YC_LANG_ISSUES_TECH ?></label></th>
	<td>
	<?php
	foreach (explode("\n", $issue['tech_url']) as $tech_url):
		echo '<a href="'.$tech_url.'">'.$tech_url.'</a>';
	endforeach;
	?>
	</td>
</tr>
<?php endif; ?>
</table>

<h2><?php echo A11YC_LANG_ISSUES_MESSAGE ?></h2>
<?php
foreach ($bbss as $bbs):
	echo '<h3><label for="a11yc_issuesbbs_'.$bbs['id'].'">'.$users[$bbs['uid']].' ('.$bbs['created_at'].')</label></h3>';
	echo '<textarea name="a11yc_issuesbbs['.$bbs['id'].']" id="a11yc_issuesbbs_'.$bbs['id'].'" cols="35" rows="7">'.$bbs['message'].'</textarea>';
endforeach;
?>
<h3><?php echo A11YC_LANG_ISSUES_MESSAGE_ADD ?></h3>
<textarea name="a11yc_issuesbbs[new]" id="a11yc_issuesbbs_new" cols="35" rows="7"></textarea>
