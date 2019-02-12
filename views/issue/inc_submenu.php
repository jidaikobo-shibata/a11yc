<?php namespace A11yc; ?>
<ul class="a11yc_submenu">
<?php

foreach ($issues as $status => $issue):

	$cnt = 0;
	foreach ($issue as $type => $vals):
		foreach ($vals as $criterion => $v):
			$cnt = $cnt + count($v);
		endforeach;
	endforeach;

?>
	<li><a href="<?php echo A11YC_ISSUE_URL.$status ?>"><?php echo constant('A11YC_LANG_ISSUE_TITLE_'.strtoupper($status)) ?> (<?php echo $cnt ?>)</a></li>
<?php
endforeach;
?>
	<li><a href="<?php echo A11YC_ISSUE_URL.'index' ?>"><?php echo A11YC_LANG_ISSUE_TECH_FAILURE ?></a></li>
	<li><a href="<?php echo A11YC_ISSUE_URL.'add' ?>"><?php echo A11YC_LANG_ISSUE_ADD ?></a></li>
	<li><a href="<?php echo A11YC_DOWNLOAD_URL.'issue' ?>"><?php echo A11YC_LANG_ISSUE_EXPORT ?></a></li>
</ul>
