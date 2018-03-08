<?php namespace A11yc; ?>
<h2><?php echo A11YC_LANG_RELATED ?></h2>
<ul>
	<li><?php echo '<a href="'.$policy_link.'">'.A11YC_LANG_POLICY.'</a>'; ?></li>
	<?php if ($settings['selected_method'] !== 0 && isset($is_total) && $is_total == FALSE): ?>
		<li><?php echo '<a href="'.$report_link.'">'.A11YC_LANG_REPORT.'</a>'; ?></li>
	<?php endif; ?>
</ul>
