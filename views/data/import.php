<?php namespace A11yc; ?>
<p><?php echo $describe ?></p>
<form action="<?php echo A11YC_DATA_URL ?>import<?php echo $is_icl ? '&amp;target=icl' : '' ; ?>" method="POST" enctype="multipart/form-data">

<label><?php echo A11YC_LANG_PAGE_LABEL_IMPORT_CHOOSE_FILE ?> <input type="file" name="import"></label>
<input type="submit" class="primary" value="<?php echo A11YC_LANG_CTRL_SEND ?>">

</form>

<p><a href="?c=center&amp;a=index"><?php echo A11YC_LANG_CENTER_TITLE ?></a></p>
