<?php namespace A11yc; ?>
<ul>
<?php
foreach ($issues as $status => $issue):
?>
	<li><a href="<?php echo A11YC_ISSUES_BASE_URL.$status ?>"><?php echo constant('A11YC_LANG_ISSUES_TITLE_'.strtoupper($status)) ?> (<?php echo count($issue) ?>)</a></li>
<?php
endforeach;
?>
	<li><a href="<?php echo A11YC_ISSUES_BASE_URL.'index' ?>"><?php echo A11YC_LANG_ISSUES_TECH_FAILURE ?></a></li>
	<li><a href="<?php echo A11YC_ISSUES_BASE_URL.'add' ?>"><?php echo A11YC_LANG_ISSUES_ADD ?></a></li>
</ul>
