<?php namespace A11yc; ?>
<form id="a11yc_search" action="<?php echo A11YC_URL ?>" method="GET">
	<input type="hidden" name="c" value="docs">
	<input type="hidden" name="a" value="index">
	<label class="a11yc_skip" for="a11yc_str"><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?></label><input type="text" name="s" id="a11yc_str" size="24" value="<?php echo $word ?>">
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEARCH ?>">
</form>