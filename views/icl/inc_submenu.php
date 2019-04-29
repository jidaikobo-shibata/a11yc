<?php namespace A11yc; ?>

<ul>
	<li><a href="<?php echo A11YC_ICL_URL ?>index"><?php echo A11YC_LANG_ICL_TITLE ?></a></li>
	<li><a href="<?php echo A11YC_ICL_URL ?>edit&amp;is_sit=1"><?php echo A11YC_LANG_ICL_NEW_SITUATION ?></a></li>
	<li><a href="<?php echo A11YC_ICL_URL ?>edit"><?php echo A11YC_LANG_ICL_NEW ?></a></li>
	<li><a href="<?php echo A11YC_ICL_URL ?>view"><?php echo A11YC_LANG_ICL_TITLE_VIEW ?></a></li>

<?php if (empty(Model\Setting::fetch('is_waic_imported'))): ?>
	<li><a href="<?php echo A11YC_ICL_URL ?>import"><?php echo A11YC_LANG_ICL_IMPORT_WAIC ?></a></li>
<?php endif; ?>
</ul>
