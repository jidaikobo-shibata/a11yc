<?php namespace A11yc; ?>

<h2 id="a11yc_index_title"><?php echo $title ?></h2>
<?php include('inc_submenu.php'); ?>

<?php
if (empty($items)):
?>
	<p>No specified issues Found</p>
<?php
else:
?>
<table class="a11yc_table">
<thead>
<tr>
	<th scope="col" class="a11yc_result" style="min-width: 12em;">URL</th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CTRL_CHECK ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_ISSUE_TITLE ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CTRL_ACT ?></th>
</tr>
</thead>

	<?php

	foreach ($items as $url => $vals):
		$rowspan = 0;

		// common issue only
		if (isset($vals['common']) && count($vals) == 1) continue;

		// empty common issue
		if ($url != 'commons' && isset($vals['common'])) unset($vals['common']);

		foreach ($vals as $criterion => $each_issues):
			 $rowspan = $rowspan + count($each_issues);
		endforeach;
	?>
<tr>
	<th scope="row" rowspan="<?php echo $rowspan ?>" class="a11yc_issue_url">
	<?php
	if ($url == 'commons' || empty($url)):
		echo A11YC_LANG_ISSUE_IS_COMMON;
	else:
		$page = Model\Page::fetch($url);
		echo $page['title'].'<br><a href="'.Util::urldec($url).'">'.Util::s($url).'</a>';
	endif;
	?>
	</th>
	<td class="a11yc_result" rowspan="<?php echo $rowspan ?>">
	<?php if ($url != 'common' && ! empty($url)): ?>
		 <a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
	<?php endif; ?>
	</td>
<?php
	$r = 0;
	foreach ($vals as $criterion => $each_issues):
?>

<?php foreach ($each_issues as $each_issue): ?>
<?php
if( $r !== 0 ) echo '<tr>';
?>
	<td class="a11yc_issue_data">
		<?php
			$type = $each_issue['n_or_e'] == 0 ?
							'<span class="a11yc_validation_code_notice">NOTICE</span>':
							'<span class="a11yc_validation_code_error">ERROR!</span>';
		?>
		<?php echo $type ?>
			<?php if ($each_issue['trash'] != 1): ?>
			<a href="<?php echo A11YC_ISSUE_URL.'read&amp;id='.intval($each_issue['id']) ?>">
			<?php endif; ?>
				<?php echo $each_issue['id'].': '.$each_issue['title'] ?>
			<?php if ($each_issue['trash'] != 1): ?>
			</a>
			<?php endif; ?>
			<details><?php echo nl2br(Util::s($each_issue['error_message']))  ?></details>
	</td>

	<td class="a11yc_result" style="white-space: nowrap;">
		<?php if ($each_issue['trash'] != 0): ?>
			<a href="<?php echo A11YC_ISSUE_URL.'undelete&amp;id='.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_CTRL_UNDELETE ?></a><?php echo ' - '; ?>
			<a href="<?php echo A11YC_ISSUE_URL.'purge&amp;id='.intval($each_issue['id']) ?>" data-a11yc-confirm="<?php echo sprintf(A11YC_LANG_CTRL_CONFIRM, A11YC_LANG_CTRL_PURGE) ?>"><?php echo A11YC_LANG_CTRL_PURGE ?></a>
		<?php else: ?>
			<a href="<?php echo A11YC_ISSUE_URL.'edit&amp;id='.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_CTRL_LABEL_EDIT ?></a><?php echo ' - '; ?>
			<a href="<?php echo A11YC_ISSUE_URL.'delete&amp;id='.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_CTRL_DELETE ?></a>
		<?php endif; ?>
	</td>
</tr>
<?php $r++; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>

</table>
<?php endif; ?>
