<?php if ( ! $is_center): ?>
<h2><?php echo A11YC_LANG_RELATED ?></h2>
<ul>
	<li><?php echo '<p class="a11yc_link"><a href="'.$policy_link.'">'.A11YC_LANG_POLICY.'</a></p>'; ?></li>
	<?php if ($setup['selected_method'] !== 0 && isset($is_total) && $is_total == FALSE):  ?>
		<li><?php echo '<p class="a11yc_link"><a href="'.$report_link.'">'.A11YC_LANG_REPORT.'</a></p>'; ?></li>
	<?php endif;  ?>
</ul>
<?php endif;  ?>
