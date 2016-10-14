<form id="a11yc_docs_search" action="<?php echo A11YC_URL ?>" method="GET">
	<input type="hidden" name="c" value="pages">
	<input type="hidden" name="a" value="index">
	<input type="hidden" name="list" value="<?php echo $list ?>">

	<p>
		<label for="a11yc_str"><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?></label>
		<input type="text" name="s" id="a11yc_str" size="24" value="<?php echo $word ?>">

		<label for="a11yc_order"><?php echo A11YC_LANG_CTRL_ORDER_TITLE ?></label>
		<select name="order" id="a11yc_order">
			<option value="add_date_asc"><?php echo A11YC_LANG_PAGES_ORDER_ADD_DATE_ASC ?></option>
			<option value="add_date_desc"><?php echo A11YC_LANG_PAGES_ORDER_ADD_DATE_DESC ?></option>
			<option value="test_date_asc"><?php echo A11YC_LANG_PAGES_ORDER_TEST_DATE_ASC ?></option>
			<option value="test_date_desc"><?php echo A11YC_LANG_PAGES_ORDER_TEST_DATE_DESC ?></option>
			<option value="url_asc"><?php echo A11YC_LANG_PAGES_ORDER_URL_ASC ?></option>
			<option value="url_desc"><?php echo A11YC_LANG_PAGES_ORDER_URL_DESC ?></option>
			<option value="name_asc"><?php echo A11YC_LANG_PAGES_ORDER_PAGE_NAME_ASC ?></option>
			<option value="name_desc"><?php echo A11YC_LANG_PAGES_ORDER_PAGE_NAME_DESC ?></option>
		</select>
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</p>
</form>
