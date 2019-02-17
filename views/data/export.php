<?php namespace A11yc; ?>
<form action="<?php echo A11YC_DATA_URL ?>export" method="POST">

<table class="a11yc_table">
<tr>
	<th><label><input type="checkbox" name="targets[]" value="check"><?php echo A11YC_LANG_IMPLEMENTSLIST_EACH_RESULT ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_CHECK_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="result"><?php echo A11YC_LANG_TEST_RESULT ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_RESUL_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="page"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_PAGE_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="issue"><?php echo A11YC_LANG_ISSUE_TITLE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_ISSUE_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="bulk"><?php echo A11YC_LANG_BULK_TITLE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_BULK_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="icl"><?php echo A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_ICL_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="setting"><?php echo A11YC_LANG_SETTING_TITLE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_SETTING_EXP ?></td>
</tr>
<tr>
	<th><label><input type="checkbox" name="targets[]" value="site"><?php echo A11YC_LANG_SETTING_TITLE_SITE ?></label></th>
	<td><?php echo A11YC_LANG_EXPORT_SITE_EXP ?></td>
</tr>
</table>

<input type="submit" class="primary" value="<?php echo A11YC_LANG_CTRL_SEND ?>">

</form>

<p><a href="?c=center&amp;a=index"><?php echo A11YC_LANG_CENTER_TITLE ?></a></p>
