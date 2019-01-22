<?php namespace A11yc; ?>

<ul>
	<li><a href="?c=icl&amp;a=index"><?php echo A11YC_LANG_ICL_TITLE ?></a></li>
	<li><a href="?c=icl&amp;a=edit&amp;is_sit=1"><?php echo A11YC_LANG_ICL_NEW_SITUATION ?></a></li>
	<li><a href="?c=icl&amp;a=edit"><?php echo A11YC_LANG_ICL_NEW ?></a></li>
	<li><a href="?c=icl&amp;a=view"><?php echo A11YC_LANG_ICL_TITLE_VIEW ?></a></li>

<?php if (Input::get('a') == 'index'): ?>
	<li><a href="javascript:void(0);" onclick="$(':checkbox').attr('checked', true);"><?php echo A11YC_LANG_CTRL_LABEL_BULK_CHECK_ALL ?></a></li>
<?php endif; ?>

<?php if (empty(Model\Setting::fetch('is_waic_imported'))): ?>
	<li><a href="?c=icl&amp;a=import"><?php echo A11YC_LANG_ICL_IMPORT_WAIC ?></a></li>
<?php endif; ?>
</ul>
