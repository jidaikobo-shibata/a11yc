<?php namespace A11yc; ?>

<?php if ($failures): ?>
<h2><?php echo A11YC_LANG_ISSUES_TECH_FAILURE ?></h2>
<table class="a11yc_table">
<tr>
<?php foreach ($failures as $url => $pages): ?>
	<th scope="row" class="a11yc_issue_url"><?php echo Model\Html::fetchPageTitle($url) ?><br><a href="<?php echo Util::urldec($url) ?>"><?php echo Util::s($url) ?></a></th>
	<td><a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
	<td class="a11yc_issue_data"><ul>
	<?php foreach ($pages as $page): ?>
		<li><a href="<?php echo A11YC_REF_WCAG20_TECH_URL.$page['code'].'.html' ?>"><?php echo nl2br($yml[$page['code']]['title']) ?></a></li>
	<?php endforeach; ?>
	</ul></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<?php
foreach ($issues as $status => $issue):
	if (empty($issue)) continue;
?>
	<h2><?php echo constant('A11YC_LANG_ISSUES_TITLE_'.strtoupper($status)) ?></h2>
	<table class="a11yc_table">
	<tr>
		<?php foreach ($issue as $url => $each_issues): ?>
		<th scope="row" class="a11yc_issue_url">
		<?php if ($url == 'common'): ?>
			<?php echo A11YC_LANG_ISSUES_IS_COMMON ?>
		<?php else: ?>
			<?php echo Model\Html::fetchPageTitle($url) ?><br><a href="<?php echo Util::urldec($url) ?>"><?php echo Util::s($url) ?></a>
		<?php endif; ?>
		</th>
		<td>
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
				<a href="<?php echo A11YC_ISSUES_VIEW_URL.intval($each_issue['id']) ?>">
				<?php endif; ?>
					<?php echo $each_issue['id'].': '.nl2br(Util::s($each_issue['error_message'])) ?>
				<?php if ($each_issue['trash'] != 0): ?>
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
<?php endforeach; ?>
