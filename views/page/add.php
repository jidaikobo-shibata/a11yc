<?php namespace A11yc; ?>
<!-- list -->
<h2 id="a11yc_checklist_index_title"><?php echo A11YC_LANG_CTRL_ADDNEW ?></h2>

<?php echo $submenu; ?>

<!-- add pages form -->
<form action="<?php echo A11YC_PAGE_URL ?>add" method="POST">
<h2><label for="a11yc_add_pages"><?php echo A11YC_LANG_PAGE_URLS ?></label></h2>
<p><?php echo A11YC_LANG_PAGE_URL_FOR_EACH_LINE ?></p>

<textarea id="a11yc_add_pages" name="pages" rows="7" style="width: 100%;"><?php echo $crawled ?></textarea>
<?php echo isset($add_nonce) ? $add_nonce : ''; ?>
<label><input type="checkbox" name="force" value="1" /><?php echo A11YC_LANG_PAGE_URLS_ADD_FORCE ?></label>
<input type="submit" value="<?php echo A11YC_LANG_PAGE_URLS_ADD ?>" />
</form>

<?php if (Guzzle::envCheck()): ?>
<!-- get site urls -->
<form action="<?php echo A11YC_PAGE_URL ?>add" method="POST">
<h2><label for="a11yc_get_urls"><?php echo A11YC_LANG_PAGE_GET_URLS ?></label></h2>
<p><?php echo A11YC_LANG_PAGE_GET_URLS_EXP ?></p>

<input type="text" name="get_urls" id="a11yc_get_urls" size="60" value="<?php echo $get_urls ?>" />
<?php echo isset($get_nonce) ? $get_nonce : ''; ?>
<input type="submit" value="<?php echo A11YC_LANG_PAGE_GET_URLS_BTN ?>" />
</form>
<?php endif; ?>
