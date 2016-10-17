<form id="a11yc_docs_search" action="<?php echo A11YC_URL ?>" method="GET">
	<input type="hidden" name="c" value="pages">
	<input type="hidden" name="a" value="index">
	<input type="hidden" name="list" value="<?php echo $list ?>">

	<p>
		<label for="a11yc_str"><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?></label>
		<input type="text" name="s" id="a11yc_str" size="14" value="<?php echo $word ?>">

		<label for="a11yc_num"><?php echo A11YC_LANG_CTRL_NUM ?></label>
		<select name="num" id="a11yc_num">
			<?php
				foreach (array(25, 50, 100, 250, 500) as $v):
				$checked = isset($_GET['num']) && $_GET['num'] == $v ? ' checked="checked"' : '';
			?>
				<option value="<?php echo $v ?>"<?php echo $checked ?>><?php echo $v ?></option>
			<?php endforeach; ?>
		</select>

		<label for="a11yc_order"><?php echo A11YC_LANG_CTRL_ORDER_TITLE ?></label>
		<?php
		$opts = array(
			"add_date_desc" => constant('A11YC_LANG_PAGES_ORDER_ADD_DATE_DESC'),
			"add_date_asc" => constant('A11YC_LANG_PAGES_ORDER_ADD_DATE_ASC'),
			"date_desc" => constant('A11YC_LANG_PAGES_ORDER_TEST_DATE_DESC'),
			"date_asc" => constant('A11YC_LANG_PAGES_ORDER_TEST_DATE_ASC'),
			"url_desc" => constant('A11YC_LANG_PAGES_ORDER_URL_DESC'),
			"url_asc" => constant('A11YC_LANG_PAGES_ORDER_URL_ASC'),
			"name_desc" => constant('A11YC_LANG_PAGES_ORDER_PAGE_NAME_DESC'),
			"name_asc" => constant('A11YC_LANG_PAGES_ORDER_PAGE_NAME_ASC'),
		);
		?>
		<select name="order" id="a11yc_order">
		<?php
		foreach ($opts as $k => $v):
			$checked = isset($_GET['order']) && $_GET['order'] == $k ? ' checked="checked"' : '';
		?>
			<option value="<?php echo $k ?>"<?php echo $checked ?>><?php echo $v ?></option>
		<?php endforeach; ?>
		</select>
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</p>
</form>
