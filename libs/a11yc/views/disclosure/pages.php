<h2><?php echo $title ?></h2>
<?php if ($pages): ?>
<table class="a11yc_table">
<thead>
<tr>
	<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
	<th scope="col"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
	<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></th>
	<th scope="col"><?php echo A11YC_LANG_TEST_DATE ?></th>
</tr>
</thead>
<?php
foreach ($pages as $v):
	$url = \A11yc\Util::urldec($v['url']);
	$page_title = \A11yc\Util::s($v['page_title']);
	$chk = \A11yc\Util::add_query_strings(
		\A11yc\Util::uri(),
		array(
			array('url', \A11yc\Util::urlenc($url)),
			array('a11yc_checklist', 1)
		));
	$chk = \A11yc\Util::remove_query_strings($chk, array('a11yc_pages'));
?>
<tr>
	<td style="word-break: break-all;"><?php echo $page_title.'<br /><a href="'.$url.'">'.$url.'</a>' ?></td>
	<td style="text-align: center;"><?php echo \A11yc\Util::num2str($v['level']) ?></td>
	<td style="text-align: center;"><a href="<?php echo $chk ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
	<td style="white-space: nowrap;"><?php echo \A11yc\Util::s($v['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php
else:
echo A11YC_LANG_PAGES_NOT_FOUND;
endif;
?>

<h2><?php echo A11YC_LANG_REPORT ?></h2>
<p class="a11yc_link"><a href="<?php echo $report_link ?>"><?php echo A11YC_LANG_REPORT ?></a></p>