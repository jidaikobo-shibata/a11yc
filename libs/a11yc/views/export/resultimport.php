<?php namespace A11yc; ?>
<p><?php echo A11YC_LANG_PAGES_LABEL_IMPORT_CHECK_RESULT_EXP ?></p>
<form action="<?php echo A11YC_EXPORT_URL ?>resultimport" method="POST">
<textarea style="width: 100%; height: 10em" name="result"><?php
?></textarea>
<input type="submit" class="primary" value="<?php echo A11YC_LANG_CTRL_SEND ?>">

</form>

<p><a href="?c=center&amp;a=index"><?php echo A11YC_LANG_CENTER_TITLE ?></a></p>
