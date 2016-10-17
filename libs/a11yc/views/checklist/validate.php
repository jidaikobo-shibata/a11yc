<?php if ($errs):
	// error
?>
	<ul class="a11yc_hide_if_fixedheader">
	<?php foreach ($errs as $err):  ?>
		<li><?php echo $err ?></li>
	<?php endforeach;  ?>
		<li class="a11yc_disclosure_parent">
			<a role="button" class="a11yc_disclosure" tabindex="0"><?php echo A11YC_LANG_CHECKLIST_VIEW_SOURCE ?></a>
			<div class="a11yc_disclosure_target a11yc_source" style="display: block;">
				<table><?php echo $raw ?></table>
			</div><!-- /.a11yc_disclosure_target -->
		</li>
	</ul>
<?php else:
	echo '<p class="a11yc_hide_if_fixedheader">'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif; ?>
<!-- /#a11yc_errors -->
