<?php namespace A11yc; ?>
<!-- list -->
<h2 id="a11yc_checklist_index_title"><?php echo A11YC_LANG_CTRL_LABEL_EDIT ?></h2>

<?php echo $submenu; ?>

<!-- add pages form -->
<form action="<?php echo A11YC_PAGE_URL ?>edit&amp;url=<?php echo $url ?>" method="POST" enctype="multipart/form-data">
<h2><label for="a11yc_title"><?php echo A11YC_LANG_PAGE_PAGETITLE ?></label></h2>
<input type="text" name="title" id="a11yc_title" size="120" value="<?php echo Util::s($page_title) ?>" />

<h2><label for="a11yc_seq"><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></label></h2>
<input type="text" name="seq" id="a11yc_seq" size="120" value="<?php echo intval(Arr::get($page, 'seq', 0)) ?>" />

<h2><label for="a11yc_add_pages">HTML</label></h2>
<p><?php echo A11YC_LANG_PAGE_LABEL_HTML_EXP ?></p>
<textarea id="a11yc_add_pages" name="html" rows="7" style="width: 100%;"><?php echo $html ?></textarea>

<h2><label for="a11yc_title"><?php echo A11YC_LANG_ISSUE_SCREENSHOT ?></label></h2>
<input type="text" name="file_path" value="<?php echo Util::s(Arr::get($page, 'image_path', '')) ?>"/>
<input type="file" name="file" value=""/>
<?php
if (Arr::get($page, 'image_path', '')):
	echo '<div><img src="'.A11YC_UPLOAD_URL.'/'.Model\Data::groupId().'/pages/'.$url_path.'/'.Arr::get($page, 'image_path', '').'" alt="" /></div>';
endif;
?>

<?php echo isset($add_nonce) ? $add_nonce : ''; ?>

<div id="a11yc_submit">
<label for="a11yc_operation"><?php echo A11YC_LANG_CTRL_ACT ?></label>
<select name="operation" id="a11yc_operation">
	<?php if (Arr::get($page, 'trash', '') == 1): ?>
	<option value="undelete"><?php echo A11YC_LANG_CTRL_UNDELETE ?></option>
	<option value="purge"><?php echo A11YC_LANG_CTRL_PURGE ?></option>
	<?php else: ?>
	<option value="save"><?php echo A11YC_LANG_CTRL_SAVE ?></option>
	<option value="check"><?php echo A11YC_LANG_CTRL_CHECK ?></option>
	<option value="result"><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></option>
	<option value="live"><?php echo A11YC_LANG_PAGE_LIVE ?></option>
	<option value="image"><?php echo A11YC_LANG_IMAGE ?></option>
	<option value="export"><?php echo A11YC_LANG_PAGE_EXPORT ?></option>
	<option value="delete"><?php echo A11YC_LANG_CTRL_DELETE ?></option>
	<?php endif; ?>
</select>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>
</form>