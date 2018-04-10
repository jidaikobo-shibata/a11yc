<?php namespace A11yc; ?>
<!-- list -->
<h2 id="a11yc_checklist_index_title"><?php echo A11YC_LANG_PAGES_INDEX ?></h2>

<!-- /.a11yc_checklist_pages -->
<?php

echo $submenu;

if ($pages):

	// show search and order form
	echo $search_form;
?>

<form action="<?php echo Util::uri() ?>" method="POST">
<label for="a11yc_operation"><?php echo A11YC_LANG_BULK_TITLE ?></label>
<select name="operation" id="a11yc_operation">
	<?php if ($list == 'trash'): ?>
	<option></option>
	<option value="undelete"><?php echo A11YC_LANG_PAGES_UNDELETE ?></option>
	<option value="purge"><?php echo A11YC_LANG_PAGES_PURGE ?></option>
	<?php else: ?>
	<option></option>
	<option value="result"><?php echo A11YC_LANG_PAGES_LABEL_EXPORT_RESULT_HTML ?></option>
	<option value="export"><?php echo A11YC_LANG_PAGES_EXPORT ?></option>
	<option value="delete"><?php echo A11YC_LANG_PAGES_DELETE ?></option>
	<?php endif; ?>
</select>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />

	<table class="a11yc_table">
	<thead>
	<tr>
		<td><label><input type="checkbox" /><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_LABEL_BULK_CHECK_ALL ?></span></label></td>
		<th>URL</th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_DONE ?></th>
		<?php if ($list != 'trash'): ?>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CHECK ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_LIVE ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_IMAGE ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_EXPORT ?></th>
		<?php endif; ?>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CTRL ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CREATED_AT ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_TEST_DATE ?></th>
	</tr>
	</thead>

	<tbody>
	<?php
	$i = 0;
	$no_url = Util::urldec(Input::get('no_url', ''));
	foreach ($pages as $page):
	$url = Util::s($page['url']);
	$not_found_class = $no_url == $url ? ' not_found_url' : '';
	$title = Util::s($page['title']);
	$not_found_class.= $page['done'] ? ' done' : '';
	$class_str = ++$i%2==0 ? ' class="even'.$not_found_class.'"' : ' class="odd'.$not_found_class.'"';
	?>
	<tr<?php echo $class_str ?>>
		<td><label><input type="checkbox" name="bulk[<?php echo $url ?>]" value="1" /><span class="a11yc_skip"><?php echo $title ?></span></label></td>
		<th scope="row">
			<?php echo $no_url == $url ? '<div><strong>'.A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR.'</strong></div>' : '' ?>
			<?php echo $title.'<br /><a href="'.$url.'">'.$url ?></a>
		</th>

		<td class="a11yc_result"><?php echo Util::num2str($page['level']) ?></td>
		<?php
			$done = @$page['done'] == 1 ? A11YC_LANG_PAGES_DONE : '' ;
		?>
		<td class="a11yc_result"><?php echo $done ?></td>

		<?php if ($list != 'trash'): ?>
		<td class="a11yc_result"><a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

		<td class="a11yc_result">
		<?php if ($page['done']): ?>
<a href="<?php echo A11YC_RESULTS_EACH_URL.Util::urlenc($url) ?>" class="a11yc_hasicon" target="a11yc_live"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_html a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
		<?php endif; ?>
		</td>

		<td class="a11yc_result"><a href="<?php echo A11YC_LIVE_URL.Util::urlenc($url).'&amp;base_url='.Util::urlenc($settings['base_url']) ?>" class="a11yc_hasicon" target="a11yc_live"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_LIVE ?></span><span class="a11yc_icon_live a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

		<td class="a11yc_result"><a href="<?php echo A11YC_IMAGELIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon" target="a11yc_images"><span class="a11yc_skip"><?php echo A11YC_LANG_IMAGE ?></span><span class="a11yc_icon_images a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

		<td class="a11yc_result"><a href="<?php echo A11YC_EXPORT_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_EXPORT ?></span><span class="a11yc_icon_export a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<?php endif; ?>

		<td class="a11yc_result"><a href="<?php echo A11YC_PAGES_EDIT_URL ?>&amp;url=<?php echo Util::urlenc($url) ?>" class="a11yc_hasicon"><?php echo A11YC_LANG_PAGES_CTRL ?><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_DELETE ?></span><!-- <span class="a11yc_icon_delete a11yc_icon_fa" role="presentation" aria-hidden="true"></span> --></a></td>

		<td class="a11yc_result"><?php echo $page['created_at'] ? date('Y-m-d', strtotime($page['created_at'])) : '-' ?></td>
		<td class="a11yc_result"><?php echo $page['date'] ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

</form>
<?php else: ?>
	<p><?php echo A11YC_LANG_PAGES_NOT_FOUND ?></p>
<?php endif; ?>
