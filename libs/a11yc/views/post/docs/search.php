<?php namespace A11yc; ?>
<form id="a11yc_search" action="<?php echo $base_url ?>" method="GET">
	<input type="hidden" name="a" value="docs">
	<label class="a11yc_skip" for="a11yc_str"><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?></label><input type="text" name="s" id="a11yc_str" size="24" value="<?php echo $word ?>">
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEARCH ?>">
</form>