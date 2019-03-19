<?php
namespace A11yc;
$selected_str = ' selected="selected"';
?>

<form action="<?php echo A11YC_SITECHECK_URL ?>index" method="POST">
<p>
<label><?php echo A11YC_LANG_CTRL_KEYWORD_TITLE ?> <input type="text" name="keyword" size="20" value="<?php echo $keyword ?>"></label>

<?php $checked = $use_re ? ' checked="checked"' : ''; ?>
<label><input type="checkbox"<?php echo $checked ?> name="use_re" value="1"> <?php echo A11YC_LANG_USE_RE  ?></label>
</p>
<p>
<label><?php echo A11YC_LANG_CTRL_CHECK ?><select name="op">
	<?php $selected = Input::post('op') == 'contain_tabindex' ? $selected_str : '' ; ?>
	<option value="contain_keyword"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_CTRL_KEYWORD_TITLE) ?></option>

	<?php $selected = Input::post('op') == 'contain_positivetabindex' ? $selected_str : '' ; ?>
	<option value="contain_positivetabindex"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_POSITIVETABINED) ?></option>

	<?php $selected = Input::post('op') == 'contain_withoutalt' ? $selected_str : '' ; ?>
	<option value="contain_withoutalt"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_WITHOUT_ALT) ?></option>

	<?php $selected = Input::post('op') == 'contain_withoutth' ? $selected_str : '' ; ?>
	<option value="contain_withoutth"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_WITHOUT_TH) ?></option>

	<?php $selected = Input::post('op') == 'contain_not_headings' ? $selected_str : '' ; ?>
	<option value="contain_not_headings"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS_NOT, "h*") ?></option>

	<?php $selected = Input::post('op') == 'contain_not_href' ? $selected_str : '' ; ?>
	<option value="contain_not_href"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_WITHOUT_HREF) ?></option>
</select></label>
</p>
<input type="submit" name="str" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>
