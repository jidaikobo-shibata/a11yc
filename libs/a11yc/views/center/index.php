<h2><?php echo A11YC_LANG_TEST_RESUST ?></h2>
<table class="a11yc_table">
<tbody>
	<tr>
		<th><!-- target level --><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><p><?php echo \A11YC\Util::num2str($target_level) ?></p></td>
	</tr>
	<tr>
		<th><!-- current level --><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td><p>
<?php
$site_level = \A11YC\Evaluate::check_site_level();
echo \A11YC\Evaluate::result_str($site_level, $target_level);
?>
		</p></td>
	</tr>
	<tr>
		<th><!-- selected method --><?php echo A11YC_LANG_CANDIDATES0 ?></th>
<?php
$arr = array(
  A11YC_LANG_CANDIDATES1,
  A11YC_LANG_CANDIDATES2,
  A11YC_LANG_CANDIDATES3,
  A11YC_LANG_CANDIDATES4,
);
?>
		<td><p><?php echo $arr[$selected_method] ?></p></td>
	</tr>
	<tr>
		<th><!-- number of checked --><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><p><?php echo $done['done'].' / '.$total['total'] ?></p></td>
	</tr>
	<tr>
		<th><!-- unpassed pages --><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
			<ul>
<?php
$unpassed_pages = \A11yc\Evaluate::unpassed_pages($target_level);
if ($unpassed_pages):
?>
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
<?php else: ?>
			<p><?php echo A11YC_LANG_UNPASSED_PAGES_NO ?></p>
<?php endif; ?>
		</td>
	</tr>
</table>

<!-- site results -->
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>