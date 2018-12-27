<?php namespace A11yc; ?>
<ul>
<?php
foreach ($issues as $status => $issue):
?>
	<li><a href="<?php echo A11YC_ISSUES_URL.$status ?>"><?php echo constant('A11YC_LANG_ISSUES_TITLE_'.strtoupper($status)) ?> (<?php echo count($issue) ?>)</a></li>
<?php
endforeach;
?>
	<li><a href="<?php echo A11YC_ISSUES_URL.'index' ?>"><?php echo A11YC_LANG_ISSUES_TECH_FAILURE ?></a></li>
	<li><a href="<?php echo A11YC_ISSUES_URL.'add' ?>"><?php echo A11YC_LANG_ISSUES_ADD ?></a></li>
	<li><a href="<?php echo A11YC_EXPORT_URL.'issue' ?>"><?php echo A11YC_LANG_ISSUES_EXPORT ?></a></li>
</ul>
