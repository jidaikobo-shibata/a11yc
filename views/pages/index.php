<!-- form -->
<form action="" method="POST">
<h2><label for="a11yc_pages"><?php echo A11YC_LANG_PAGES_URLS ?></label></h2>
<textarea id="a11yc_pages" name="pages" rows="7" style="width: 100%;">
</textarea>
<input type="submit" value="<?php echo A11YC_LANG_PAGES_URLS_ADD ?>" />
</form>

<!-- list -->
<h2><?php echo A11YC_LANG_PAGES_TITLE ?></h2>
<p><a href="<?php echo A11YC_PAGES_URL ?>">pages</a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=yet">yet</a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=done">done</a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=trash">trash</a></p>

<?php if ($pages): ?>
	<table class="a11yc_tbl">
	<thead>
	<th>URL</th>
	<th>Level</th>
	<th>Done</th>
	<th>Check</th>
	<th>Delete</th>
	</thead>
	<?php
	foreach ($pages as $page):
	$url = \A11yc\Util::s($page['url']);
	?>
	<tr>
		<th><?php echo $url ?></th>
		<td><?php echo \A11yc\Util::num2str($page['level']) ?></td>
		<?php $done = @$page['done'] == 1 ? 'Done' : '' ; ?>
		<td><?php echo $done ?></td>
		<td><a href="<?php echo A11YC_CHECKLIST_URL.urlencode($url) ?>">Check</a></td>
		<?php if ($list == 'trash'): ?>
			<td><a href="<?php echo A11YC_PAGES_URL ?>&amp;undel=1&amp;url=<?php echo urlencode($url) ?>">Undelete</a></td>
		<?php else: ?>
			<td><a href="<?php echo A11YC_PAGES_URL ?>&amp;del=1&amp;url=<?php echo urlencode($url) ?>">Delete</a></td>
		<?php endif; ?>
	</tr>
	<?php endforeach; ?>
	</table>
<?php else: ?>
	<p><?php echo A11YC_LANG_PAGES_NOT_FOUND ?></p>
<?php endif; ?>
