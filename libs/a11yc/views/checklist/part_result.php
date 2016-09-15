<table class="a11yc_table">
	<thead>
		<tr>
			<th scope="col" colspan="2"><?php echo A11YC_LANG_CRITERION ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_EXIST ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PASS ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($yml['criterions'] as $k => $v):
	if (
		($include && strlen($v['level']['name']) <= $target_level) ||
		( ! $include && strlen($v['level']['name']) > $target_level)
	):
		if ( ! $results[$k]['pass']) continue;
?>
		<tr>
			<th><?php echo \A11yc\Util::key2code($k) ?></th>
			<td><?php echo $v['name'] ?></td>
			<td class="a11yc_result"><?php echo $v['level']['name'] ?></td>
			<td class="a11yc_result">
			<?php echo isset($results[$k]['non_exist']) ? A11YC_LANG_EXIST_NON : '-' ?>
			</td>
			<td class="a11yc_result">
			<?php echo $results[$k]['pass'] ? A11YC_LANG_PASS : '-' ?>
			</td>
		</tr>
<?php
	endif;
endforeach;
?>
	</tbody>
</table><!--/.a11yc_table-->
</section>
