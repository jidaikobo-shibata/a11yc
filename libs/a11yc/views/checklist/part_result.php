<table>
<thead>
<tr>
<th colspan="2"><?php echo A11YC_LANG_CRITERION ?></th>
<th><?php echo A11YC_LANG_LEVEL ?></th>
<th><?php echo A11YC_LANG_EXIST ?></th>
<th><?php echo A11YC_LANG_PASS ?></th>
</tr>
</thead>

<?php
	foreach ($yml['criterions'] as $k => $v):
	if (
		($include && strlen($v['level']['name']) <= $target_level) ||
		( ! $include && strlen($v['level']['name']) > $target_level)):
?>
		<tr>
		<th><?php echo \A11yc\Util::key2code($k) ?></th>
		<td><?php echo $v['name'] ?></td>
		<td><?php echo $v['level']['name'] ?></td>
		<td>
		<?php echo isset($results[$k]['non_exist']) ? A11YC_LANG_EXIST_NON : '-' ?>
		</td>
		<td>
		<?php echo $results[$k]['pass'] ? A11YC_LANG_PASS : '-' ?>
		</td>
		</tr>
<?php
	endif;
endforeach;
?>
</table>
</section>
