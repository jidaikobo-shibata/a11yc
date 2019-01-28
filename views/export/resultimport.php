<?php namespace A11yc; ?>
<p><?php echo A11YC_LANG_PAGE_LABEL_IMPORT_CHECK_RESULT_EXP ?></p>
<form action="<?php echo A11YC_EXPORT_URL ?>resultimport" method="POST" enctype="multipart/form-data">

<label><?php echo A11YC_LANG_PAGE_LABEL_IMPORT_CHOOSE_FILE ?> <input type="file" name="import"></label>
<input type="submit" class="primary" value="<?php echo A11YC_LANG_CTRL_SEND ?>">

</form>

<p><a href="?c=center&amp;a=index"><?php echo A11YC_LANG_CENTER_TITLE ?></a></p>
