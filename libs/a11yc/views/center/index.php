<h2><?php echo A11YC_LANG_TEST_RESULT ?></h2>
<table class="a11yc_table">
<tbody>
	<tr>
		<th><!-- target level --><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><?php echo \A11YC\Util::num2str($target_level) ?></td>
	</tr>
	<tr>
		<th><!-- current level --><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td>
<?php
$site_level = \A11YC\Evaluate::check_site_level();
echo \A11YC\Evaluate::result_str($site_level, $target_level);
?>
		</td>
	</tr>
	<tr>
		<th><!-- selected method --><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
<?php
$arr = array(
  A11YC_LANG_CANDIDATES1,
  A11YC_LANG_CANDIDATES2,
  A11YC_LANG_CANDIDATES3,
  A11YC_LANG_CANDIDATES4,
);
?>
		<td><?php echo $arr[$selected_method] ?></td>
	</tr>
<?php if (isset($done)): ?>
	<tr>
		<th><!-- number of checked --><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><?php echo $done['done'].' / '.$total['total'] ?></td>
	</tr>
<?php endif; ?>
<?php if (isset($unpassed_pages)): ?>
	<tr>
		<th><!-- unpassed pages --><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
			<?php
			if ($unpassed_pages):
			?>
			<ul>
			<?php
			foreach ($unpassed_pages as $v):
				$url = s($v['url']);
			?>
				<li>
					<a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $url ?></a>
					(<a href="<?php echo A11YC_CHECKLIST_URL.$url ?>"<?php echo A11YC_TARGET ?>>check</a>)
				</li>
			<?php endforeach; ?>
			</ul>
			<?php elseif (count($passed_pages) >= 1): ?>
			<?php echo A11YC_LANG_UNPASSED_PAGES_NO ?>
			<?php else: ?>
			<?php echo '-' ?>
<?php endif; ?>
		</td>
	</tr>
<?php endif; ?>
</table>

<?php if ($result): ?>
<!-- site results -->
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>
<?php endif; ?>
