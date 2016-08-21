<h2><?php echo A11YC_LANG_SETUP_TITLE ?></h2>
<h3><label for="a11yc_declare_date"><?php echo A11YC_LANG_DECLARE_DATE ?></label></h3>
<div><input type="text" name="declare_date" id="a11yc_declare_date" size="10" value="<?php echo @$setup['declare_date'] ?>"></div>

<h2><label for="a11yc_standard"><?php echo A11YC_LANG_STANDARD ?></label></h2>
<div><select name="standard" id="a11yc_standard">
<?php
	foreach ($standards['standards'] as $k => $v):
	$selected = $k == @$setup['standard'] ? ' selected="selected"' : '';
?>
	<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
<?php endforeach;  ?>
</select></div>

<h3><label for="a11yc_target_level"><?php echo A11YC_LANG_TARGET_LEVEL ?></label></h3>
<div><select name="target_level" id="a11yc_target_level">
<?php
	foreach (array('', 'A', 'AA', 'AAA') as $k => $v):
	$selected = @$setup['target_level'] == $k ? ' selected="selected"' : '';
?>
	<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
<?php endforeach; ?>
</select></div>

<h3><label for="a11yc_selected_method"><?php echo A11YC_LANG_CANDIDATES0 ?></label></h3>
<div><select name="selected_method" id="a11yc_selected_method">
<?php
	$selected_methods = array(
		A11YC_LANG_CANDIDATES1,
		A11YC_LANG_CANDIDATES2,
		A11YC_LANG_CANDIDATES3,
		A11YC_LANG_CANDIDATES4,
	);
	foreach ($selected_methods as $k => $v):
	$selected = @$setup['selected_method'] == $k ? ' selected="selected"' : '';
?>
	<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
<?php endforeach; ?>
</select></div>

<h3><label for="a11yc_test_period"><?php echo A11YC_LANG_TEST_PERIOD ?></label></h3>
<div><input type="text" name="test_period" id="a11yc_test_period" size="20" value="<?php echo s(@$setup['test_period']) ?>"></div>

<h3><label for="a11yc_dependencies"><?php echo A11YC_LANG_DEPENDENCIES ?></label></h3>
<div><textarea name="dependencies" id="a11yc_dependencies" style="width:100%;" rows="7"><?php echo s(@$setup['dependencies']) ?></textarea></div>

<h3><label for="a11yc_policy"><?php echo A11YC_LANG_POLICY ?></label></h3>
<p><?php echo A11YC_LANG_POLICY_DESC ?></p>
<div><textarea name="policy" id="a11yc_policy" style="width:100%;" rows="7"><?php echo s(@$setup['policy']) ?></textarea></div>

<h3><label for="a11yc_report"><?php echo A11YC_LANG_REPORT ?></label></h3>
<p><?php echo A11YC_LANG_REPORT_DESC ?></p>
<div><textarea name="report" id="a11yc_report" style="width:100%;" rows="7"><?php echo s(@$setup['report']) ?></textarea></div>

<h3><label for="a11yc_contact"><?php echo A11YC_LANG_CONTACT ?></label></h3>
<p><?php echo A11YC_LANG_CONTACT_DESC ?></p>
<div><textarea name="contact" id="a11yc_contact" style="width:100%;" rows="7"><?php echo s(@$setup['contact']) ?></textarea></div>

<h2><?php echo A11YC_LANG_SETUP_TITLE_ETC ?></h2>
<?php $checked = @$setup['checklist_behaviour'] ? ' checked="checked"' : '';  ?>
<div><label for="a11yc_checklist_behaviour"><input type="checkbox" name="checklist_behaviour" id="a11yc_checklist_behaviour" value="1"<?php echo $checked ?> /><?php echo A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR ?></label></div>
