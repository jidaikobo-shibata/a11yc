<!-- list -->
<h2><?php echo A11YC_LANG_PAGES_TITLE ?></h2>
<p><a href="<?php echo A11YC_PAGES_URL ?>"><?php echo A11YC_LANG_PAGES_ALL ?></a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=yet"><?php echo A11YC_LANG_PAGES_YET ?></a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=done"><?php echo A11YC_LANG_PAGES_DONE ?></a> |
<a href="<?php echo A11YC_PAGES_URL ?>&amp;list=trash"><?php echo A11YC_LANG_PAGES_TRASH ?></a></a></p>

<?php
if ($pages):
	// show search and order form
	echo $search_form;

	// pagination
	$pagination = '';
	if ($prev || $next):
	if ($prev):
		$pagination.= '<a href="'.$prev.'">'.A11YC_LANG_CTRL_PREV.'</a>';
	endif;
	if ($next):
		$pagination.= '<a href="'.$next.'">'.A11YC_LANG_CTRL_NEXT.'</a>';
	endif;
	endif;
	echo $pagination;
?>

	<table class="a11yc_table">
	<thead>
	<th>URL</th>
	<th class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
	<th class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_DONE ?></th>
	<th class="a11yc_result"><?php echo A11YC_LANG_PAGES_CHECK ?></th>
	<th class="a11yc_result"><?php echo A11YC_LANG_PAGES_CTRL ?></th>
	<th class="a11yc_result"><?php echo A11YC_LANG_PAGES_ADD_DATE ?></th>
	<th class="a11yc_result"><?php echo A11YC_LANG_TEST_DATE ?></th>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($pages as $page):
	$url = \A11yc\Util::s($page['url']);
	$page_title = \A11yc\Util::s($page['page_title']);
	$class_str = ++$i%2==0 ? ' class="even"' : ' class="odd"';
	?>
	<tr<?php echo $class_str ?>>
		<th><?php echo $page_title.'<br /><a href="'.$url.'">'.$url ?></a></th>
		<td class="a11yc_result"><?php echo \A11yc\Util::num2str($page['level']) ?></td>
		<?php $done = @$page['done'] == 1 ? A11YC_LANG_PAGES_DONE : '' ; ?>
		<td class="a11yc_result"><?php echo $done ?></td>
		<td class="a11yc_result"><a href="<?php echo A11YC_CHECKLIST_URL.urlencode($url) ?>"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check" role="presentation" aria-hidden="true"></span></a></td>
		<?php if ($list == 'trash'): ?>
			<td class="a11yc_result">
				<a href="<?php echo A11YC_PAGES_URL ?>&amp;undel=1&amp;url=<?php echo urlencode($url) ?>"><?php echo A11YC_LANG_PAGES_UNDELETE ?></a>
				<a href="<?php echo A11YC_PAGES_URL ?>&amp;purge=1&amp;url=<?php echo urlencode($url) ?>"><?php echo A11YC_LANG_PAGES_PURGE ?></a>
			</td>

		<?php else: ?>
			<td class="a11yc_result"><a href="<?php echo A11YC_PAGES_URL ?>&amp;del=1&amp;url=<?php echo urlencode($url) ?>"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_DELETE ?></span><span class="a11yc_icon_delete" role="presentation" aria-hidden="true"></span></a></td>
		<?php endif; ?>
		<td><?php echo date('Y-m-d', strtotime($page['add_date'])) ?></td>
		<td><?php echo $page['date'] ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php
	// pagination
	echo $pagination;
else:
?>
	<p><?php echo A11YC_LANG_PAGES_NOT_FOUND ?></p>
<?php endif; ?>

<!-- form -->
<form action="" method="POST">
<h2><label for="a11yc_pages"><?php echo A11YC_LANG_PAGES_URLS ?></label></h2>
<p><?php echo A11YC_LANG_PAGES_URL_FOR_EACH_LINE ?></p>

<textarea id="a11yc_pages" name="pages" rows="7" style="width: 100%;">
</textarea>
<input type="submit" value="<?php echo A11YC_LANG_PAGES_URLS_ADD ?>" />
</form>
