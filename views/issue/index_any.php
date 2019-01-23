<?php namespace A11yc; ?>

<?php include('inc_submenu.php'); ?>

<h2><?php echo $title ?></h2>
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

	$rowspan = 1;
	foreach ($vals as $criterion => $each_issues):
		 $rowspan = $rowspan + count($each_issues);
	endforeach;
	?>
<tr>
	<th scope="row" rowspan="<?php echo $rowspan ?>" class="a11yc_issue_url">
	<?php if ($url == 'commons' || empty($url)): ?>
		<?php echo A11YC_LANG_ISSUE_IS_COMMON ?>
	<?php else: ?>
		<?php echo Model\Html::fetchPageTitle($url) ?><br><a href="<?php echo Util::urldec($url) ?>"><?php echo Util::s($url) ?></a>
	<?php endif; ?>
	</th>
	<td class="a11yc_result" rowspan="<?php echo $rowspan ?>">
	<?php if ($url != 'common' && ! empty($url)): ?>
		 <a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
	<?php endif; ?>
	</td>
</tr>

<?php
	foreach ($vals as $criterion => $each_issues):
?>

<?php foreach ($each_issues as $each_issue): ?>
<tr>
	<td class="a11yc_issue_data"><ul>
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
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>

</table>
<?php endif; ?>
