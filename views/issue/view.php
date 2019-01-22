<?php namespace A11yc; ?>

<form action="<?php echo Util::uri() ?>" method="POST" class="a11yc">

<?php echo $form ?>

<div id="a11yc_submit">
	<a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($issue['url']) ?>"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></a>
	<?php if ($issue['uid'] == $current_user_id || $is_admin): ?>
	<a href="<?php echo A11YC_ISSUE_URL.'edit&amp;id='.intval($issue['id']) ?>"><?php echo A11YC_LANG_ISSUE_EDIT  ?></a>
	<?php endif; ?>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
</div>

</form>
