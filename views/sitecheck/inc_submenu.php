<?php
namespace A11yc;
$selected_str = ' selected="selected"';
?>

<form action="<?php echo A11YC_SITECHECK_URL ?>index" method="POST">
<label><?php echo A11YC_LANG_CTRL_CHECK ?><select name="op">
	<?php $selected = Input::post('op') == 'contain_tabindex' ? $selected_str : '' ; ?>
	<option value="contain_tabindex"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, "tabindex") ?></option>

	<?php $selected = Input::post('op') == 'contain_positivetabindex' ? $selected_str : '' ; ?>
	<option value="contain_positivetabindex"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_POSITIVETABINED) ?></option>

	<?php $selected = Input::post('op') == 'contain_withoutalt' ? $selected_str : '' ; ?>
	<option value="contain_withoutalt"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_WITHOUT_ALT) ?></option>

	<?php $selected = Input::post('op') == 'contain_withoutth' ? $selected_str : '' ; ?>
	<option value="contain_withoutth"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS, A11YC_LANG_SITECHECK_WITHOUT_TH) ?></option>

	<?php $selected = Input::post('op') == 'contain_not_headings' ? $selected_str : '' ; ?>
	<option value="contain_not_headings"<?php echo $selected ?>><?php printf(A11YC_LANG_SITECHECK_CONTAINS_NOT, "h*") ?></option>
</select></label>
<input type="submit" name="str" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>
