<?php namespace A11yc; ?>
<!-- list -->
<h2 id="a11yc_index_title"><?php echo A11YC_LANG_PAGE_INDEX ?></h2>

<!-- /.a11yc_checklist_pages -->
<?php

echo $submenu;

if ($pages):
	// show search and order form
	echo $search_form;
?>

<form action="<?php echo Util::uri() ?>" method="POST" class="a11yc_form_confirm">
<?php echo isset($add_nonce) ? $add_nonce : ''; ?>
<label for="a11yc_operation"><?php echo A11YC_LANG_BULK_TITLE ?></label>
<select name="operation" id="a11yc_operation">
	<?php if ($list == 'trash'): ?>
	<option></option>
	<option value="undelete"><?php echo A11YC_LANG_CTRL_UNDELETE ?></option>
	<option value="purge"><?php echo A11YC_LANG_CTRL_PURGE ?></option>
	<?php else: ?>
	<option></option>
	<option value="delete"><?php echo A11YC_LANG_CTRL_DELETE ?></option>
	<?php endif; ?>
</select>
<input type="submit" class="a11yc_button_inline" value="<?php echo A11YC_LANG_BULK_TITLE ?>" />

	<table class="a11yc_table">
	<thead>
	<tr>
		<td><label><input type="checkbox" /><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_LABEL_BULK_CHECK_ALL ?></span></label></td>
		<th scope="col">ID</th>
		<th scope="col">URL</th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_EXAM ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_IMAGE ?></th>
		<?php if ($list != 'trash'): ?>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CTRL_CHECK ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_TEST_RESULT ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGE_LIVE ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_IMAGES_TITLE ?></th>
		<?php endif; ?>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CTRL_ACT ?></th>
		<th id="a11yc_label_seq"><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></th>
		<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LAST_UPDATE ?></th>
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

		<td class="a11yc_result"><input type="text" name="serial_nums[<?php echo $url ?>]" aria-labelledby="a11yc_label_serial_nums" size="4" value="<?php echo Arr::get($page, 'serial_num'); ?>" /></td>

		<th scope="row">
			<?php echo $no_url == $url ? '<div><strong>'.A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR.'</strong></div>' : '' ?>
			<?php echo $title.'<br /><a href="'.$url.'">'.$url ?></a>
		</th>

		<td class="a11yc_result"><?php echo Util::num2str($page['level']) ?></td>
		<?php
			$done = Arr::get($page, 'done', 0) == 1 ? A11YC_LANG_CTRL_DONE : '' ;
		?>
		<td class="a11yc_result"><?php echo $done ?></td>

		<td class="a11yc_result"><?php
			echo Arr::get($page, 'image_path') ? 'ok' : '-' ;
		?></td>

		<?php if ($list != 'trash'): ?>
		<td class="a11yc_result"><a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

		<td class="a11yc_result">
		<?php if ($page['done']): ?>
		<a href="<?php echo A11YC_RESULT_EACH_URL.Util::urlenc($url) ?>" class="a11yc_hasicon" target="a11yc_live"><span class="a11yc_skip"><?php echo A11YC_LANG_TEST_RESULT ?></span><span class="a11yc_icon_html a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
		<?php endif; ?>
		</td>

		<td class="a11yc_result"><a href="<?php echo A11YC_LIVE_URL.Util::urlenc($url).'&amp;base_url='.Util::urlenc(Model\Data::baseUrl()) ?>" class="a11yc_hasicon" target="a11yc_live"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGE_LIVE ?></span><span class="a11yc_icon_live a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

		<td class="a11yc_result"><a href="<?php echo A11YC_IMAGELIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon" target="a11yc_images"><span class="a11yc_skip"><?php echo A11YC_LANG_IMAGE ?></span><span class="a11yc_icon_images a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<?php endif; ?>

		<td class="a11yc_result"><a href="<?php echo A11YC_PAGE_URL ?>edit&amp;url=<?php echo Util::urlenc($url) ?>" class="a11yc_hasicon"><?php echo A11YC_LANG_CTRL_ACT ?><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_DELETE ?></span><!-- <span class="a11yc_icon_delete a11yc_icon_fa" role="presentation" aria-hidden="true"></span> --></a></td>

		<td class="a11yc_result"><input type="text" name="seq[<?php echo $url ?>]" aria-labelledby="a11yc_label_seq" size="3" value="<?php echo intval($page['seq']); ?>" /></td>

		<td class="a11yc_result"><?php
		$htmldata = Model\Html::fetchRaw($url);
		$eachhtml = Arr::get($htmldata, 'using', '');
		if ( ! empty($eachhtml) && $htmldata['updated_at']):
			echo date('Y-m-d H:i', strtotime($htmldata['updated_at']));
		endif;
		?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

</form>
<?php else: ?>
	<p><?php echo A11YC_LANG_PAGE_NOT_FOUND ?></p>
<?php endif; ?>
