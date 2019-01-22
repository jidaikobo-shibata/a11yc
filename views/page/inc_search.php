<?php namespace A11yc; ?>
<form id="a11yc_search" action="<?php echo A11YC_URL ?>" method="GET">
	<input type="hidden" name="c" value="page">
	<input type="hidden" name="a" value="index">
	<input type="hidden" name="list" value="<?php echo $list ?>">

	<p>
		<label for="a11yc_str"><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?></label>
		<input type="text" name="s" id="a11yc_str" size="14" value="<?php echo $word ?>">

		<label for="a11yc_order" class="a11yc_skip"><?php echo A11YC_LANG_CTRL_ORDER_TITLE ?></label>
		<?php
		$opts = array(
			"seq_asc"         => constant('A11YC_LANG_CTRL_ORDER_SEQ_ASC'),
			"seq_desc"        => constant('A11YC_LANG_CTRL_ORDER_SEQ_DESC'),
			"created_at_asc"  => constant('A11YC_LANG_PAGE_ORDER_CREATED_AT_ASC'),
			"created_at_desc" => constant('A11YC_LANG_PAGE_ORDER_CREATED_AT_DESC'),
			"date_asc"        => constant('A11YC_LANG_PAGE_ORDER_TEST_DATE_ASC'),
			"date_desc"       => constant('A11YC_LANG_PAGE_ORDER_TEST_DATE_DESC'),
			"url_asc"         => constant('A11YC_LANG_PAGE_ORDER_URL_ASC'),
			"url_desc"        => constant('A11YC_LANG_PAGE_ORDER_URL_DESC'),
			"title_asc"       => constant('A11YC_LANG_PAGE_ORDER_TITLE_ASC'),
			"title_desc"      => constant('A11YC_LANG_PAGE_ORDER_TITLE_DESC'),
		);
		?>
		<select name="order" id="a11yc_order">
		<?php
		foreach ($opts as $k => $v):
			$checked = Input::get('order') == $k ? ' selected="selected"' : '';
		?>
			<option value="<?php echo $k ?>"<?php echo $checked ?>><?php echo $v ?></option>
		<?php endforeach; ?>
		</select>
		<input type="submit" class="a11yc_button_inline" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</p>
</form>
