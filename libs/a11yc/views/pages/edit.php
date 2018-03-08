<?php namespace A11yc; ?>
<!-- list -->
<h2 id="a11yc_checklist_index_title"><?php echo A11YC_LANG_PAGES_LABEL_EDIT ?></h2>

<?php echo $submenu; ?>

<!-- add pages form -->
<form action="<?php echo A11YC_PAGES_EDIT_URL ?>&amp;url=<?php echo $url ?>" method="POST">
<h2><label for="a11yc_add_pages"><?php echo A11YC_LANG_PAGES_PAGETITLE ?></label></h2>
<input type="text" name="title" id="title" size="40" value="<?php echo Util::s($page_title) ?>" />

<h2><label for="a11yc_add_pages">HTML</label></h2>
<p><?php echo A11YC_LANG_PAGES_LABEL_HTML_EXP ?></p>
<textarea id="a11yc_add_pages" name="html" rows="7" style="width: 100%;"><?php echo $html ?></textarea>
<?php echo isset($add_nonce) ? $add_nonce : ''; ?>

<h2><label for="a11yc_operation"><?php echo A11YC_LANG_PAGES_CTRL ?></label></h2>
<select name="operation" id="a11yc_operation">
	<option value="save"><?php echo A11YC_LANG_CTRL_SAVE ?></option>
	<option value="check"><?php echo A11YC_LANG_PAGES_CHECK ?></option>
	<option value="result"><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></option>
	<option value="live"><?php echo A11YC_LANG_PAGES_LIVE ?></option>
	<option value="image"><?php echo A11YC_LANG_IMAGE ?></option>
	<option value="export"><?php echo A11YC_LANG_PAGES_EXPORT ?></option>
	<option value="delete"><?php echo A11YC_LANG_PAGES_DELETE ?></option>
</select>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</form>
