<?php namespace A11yc; ?>

<?php include('inc_submenu.php'); ?>

<?php if ($failures): ?>
<h2><?php echo A11YC_LANG_ISSUE_TECH_FAILURE ?></h2>
<table class="a11yc_table">
<thead>
<tr>
	<th scope="col" class="a11yc_result" style="min-width: 12em;">URL</th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CTRL_CHECK ?></th>
	<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_ISSUE_TECH_RELATED ?></th>
</tr>
</thead>
<tr>
<?php
	foreach ($failures as $url => $vals):
	$page = Model\Page::fetch($url);
?>
	<th scope="row" class="a11yc_issue_url"><?php echo $page['title'] ?><br><a href="<?php echo Util::urldec($url) ?>"><?php echo Util::s($url) ?></a></th>

	<td class="a11yc_result"><a href="<?php echo A11YC_CHECKLIST_URL.Util::urlenc($url) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>

	<td class="a11yc_issue_data"><ul>
	<?php foreach ($vals as $criterion => $val): ?>
	<?php foreach ($val as $code): ?>
		<li><a href="<?php echo A11YC_REF_WCAG20_TECH_URL.$code.'.html' ?>"><?php echo nl2br($yml[$code]['title']) ?></a></li>
	<?php endforeach; ?>
	<?php endforeach; ?>
	</ul></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
