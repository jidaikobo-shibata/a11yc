<h2><?php echo $title ?></h2>
<?php if ($pages): ?>
<table class="a11yc_table">
<thead>
<tr>
	<th><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
	<th><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
	<th><?php echo A11YC_LANG_CHECKLIST_TITLE ?></th>
	<th><?php echo A11YC_LANG_TEST_DATE ?></th>
</tr>
</thead>
<?php
foreach ($pages as $v):
$url = \A11yc\Util::s($v['url']);
$chk = \A11yc\Util::add_query_strings(\A11yc\Util::uri(), array(array('url', urlencode($url))));
$chk = \A11yc\Util::remove_query_strings($chk, array('a11yc_pages'));
?>
<tr>
	<td style="word-break: break-all;"><?php echo '<a href="'.$url.'">'.$url.'</a>' ?></td>
	<td style="text-align: center;"><?php echo \A11yc\Util::num2str($v['level']) ?></td>
	<td style="text-align: center;"><?php echo '<a href="'.$chk.'">'.A11YC_LANG_CHECKLIST_TITLE.'</a>' ?></td>
	<td style="white-space: nowrap;"><?php echo \A11yc\Util::s($v['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php
else:
echo A11YC_LANG_PAGES_NOT_FOUND;
endif;
echo '<p class="a11yc_link"><a href="'.\A11yc\Util::remove_query_strings(\A11yc\Util::uri(), array('a11yc_pages')).'">'.A11YC_LANG_REPORT.'</a></p>'
?>
