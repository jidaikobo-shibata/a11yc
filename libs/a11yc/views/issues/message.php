<?php namespace A11yc; ?>

<table class="a11yc_table">

<?php if ($issue['is_common']): ?>
<tr>
	<th><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></th>
	<td><?php echo A11YC_LANG_ISSUES_IS_COMMON ?></td>
</tr>
<?php endif; ?>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_N_OR_E ?></th>
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
	<th><?php echo A11YC_LANG_ISSUES_HTML ?></th>
	<td><?php echo $issue['html'] ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_ERRMSG ?></th>
	<td><?php echo nl2br($issue['error_message']) ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_CTRL_PERSONS ?></th>
	<td><?php echo Arr::get($users, $issue['uid']) ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ISSUES_STATUS ?></th>
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
	<th><?php echo A11YC_LANG_ISSUES_TECH ?></th>
	<td>
	<?php
	foreach (explode("\n", $issue['tech_url']) as $tech_url):
		echo '<a href="'.$tech_url.'">'.$tech_url.'</a>';
	endforeach;
	?>
	</td>
</tr>
<?php endif; ?>

<?php if ( ! empty($issue['image_path'])): ?>
<tr>
	<th><?php echo A11YC_LANG_ISSUES_SCREENSHOT ?></th>
	<td>
	<?php
	echo '<div><img src="'.dirname(A11YC_URL).'/screenshots/'.$issue['id'].'/'.$issue['image_path'].'" alt="" /></div>';

	?>
	</td>
</tr>
<?php endif; ?>
</table>

<h2><?php echo A11YC_LANG_ISSUES_MESSAGE ?></h2>
<?php
foreach ($bbss as $bbs):
	echo '<h3>'.$users[$bbs['uid']].' ('.$bbs['created_at'].')</h3>';
	echo '<textarea name="a11yc_issuesbbs['.$bbs['id'].']" id="a11yc_issuesbbs_'.$bbs['id'].'" cols="35" rows="7">'.$bbs['message'].'</textarea>';
endforeach;
?>
<h3><?php echo A11YC_LANG_ISSUES_MESSAGE_ADD ?></h3>
<textarea name="a11yc_issuesbbs[new]" id="a11yc_issuesbbs_new" cols="35" rows="7"></textarea>
