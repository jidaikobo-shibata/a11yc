<?php namespace A11yc; ?>

<form action="<?php echo Util::uri() ?>" method="POST">

<?php echo $form ?>

<div id="a11yc_submit">
	<a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></a>
	<?php if ( ! $is_new): ?>
	<a href="<?php echo A11YC_ISSUES_VIEW_URL.intval($issue_id) ?>"><?php echo A11YC_LANG_ISSUES_TITLE ?></a>
	<select name="is_delete">
		<option value="0"></option>
		<option value="1"><?php echo A11YC_LANG_PAGES_PURGE; ?></option>
	</select>
	<?php endif; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>

</form>
