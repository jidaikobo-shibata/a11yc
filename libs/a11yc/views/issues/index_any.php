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
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_ISSUES_EXPORT ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PAGES_CHECK ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_ISSUES_TITLE ?></th>
</tr>
</thead>
<tr>
	<?php foreach ($items as $url => $each_issues): ?>
	<th scope="row" class="a11yc_issue_url">
	<?php if ($url == 'common' || empty($url)): ?>
		<?php echo A11YC_LANG_ISSUES_IS_COMMON ?>
	<?php else: ?>
		<?php echo Model\Html::fetchPageTitle($url) ?><br><a href="<?php echo Util::urldec($url) ?>"><?php echo Util::s($url) ?></a>
	<?php endif; ?>
	</th>

	<td class="a11yc_result">
		<a href="<?php echo A11YC_EXPORT_URL.'issue&url='.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_ISSUES_EXPORT ?></span><span class="a11yc_icon_export a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
	</td>

	<td class="a11yc_result">
	<?php if ($url != 'common'): ?>
		 <a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>
	<?php endif; ?>
	</td>

	<td class="a11yc_issue_data"><ul>
	<?php
		foreach ($each_issues as $each_issue):
		$type = $each_issue['n_or_e'] == 0 ?
						'<span class="a11yc_validation_code_notice">NOTICE</span>':
						'<span class="a11yc_validation_code_error">ERROR!</span>';
	?>
		<li><?php echo $type ?>
			<?php if ($each_issue['trash'] != 1): ?>
			<a href="<?php echo A11YC_ISSUES_READ_URL.intval($each_issue['id']) ?>">
			<?php endif; ?>
				<?php echo $each_issue['id'].': '.nl2br(Util::s($each_issue['error_message'])) ?>
			<?php if ($each_issue['trash'] != 1): ?>
			</a>
			<?php endif; ?>

		 (<?php if ($each_issue['trash'] != 0): ?>
			<a href="<?php echo A11YC_ISSUES_UNDELETE_URL.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_PAGES_UNDELETE ?></a><?php echo ' - '; ?>
			<a href="<?php echo A11YC_ISSUES_PURGE_URL.intval($each_issue['id']) ?>" data-a11yc-confirm="<?php echo sprintf(A11YC_LANG_CTRL_CONFIRM, A11YC_LANG_PAGES_PURGE) ?>"><?php echo A11YC_LANG_PAGES_PURGE ?></a>
		<?php else: ?>
			<a href="<?php echo A11YC_ISSUES_EDIT_URL.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_PAGES_LABEL_EDIT ?></a><?php echo ' - '; ?>
			<a href="<?php echo A11YC_ISSUES_DELETE_URL.intval($each_issue['id']) ?>"><?php echo A11YC_LANG_PAGES_DELETE ?></a>
		<?php endif; ?>)

		</li>
	<?php endforeach; ?>
	</ul></td>

</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
