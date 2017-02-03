<h2><?php echo $title ?></h2>
<?php
if ($pages):
foreach ($pages as $k => $each_pages):
	if ( ! $each_pages) continue;
	echo '<h3>'.$selection_reasons[$k].'</h3>';
?>
	<table class="a11yc_table a11yc_report">
	<thead>
	<tr>
		<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
		<th scope="col"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
		<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></th>
		<th scope="col"><?php echo A11YC_LANG_TEST_DATE ?></th>
	</tr>
	</thead>
	<?php
	$site_level = \A11YC\Evaluate::check_site_level();
	foreach ($each_pages as $v):
		$url = \A11yc\Util::urldec($v['url']);
		$page_title = \A11yc\Util::s($v['page_title']);
		$chk = \A11yc\Util::add_query_strings(
			\A11yc\Util::uri(),
			array(
				array('url', \A11yc\Util::urlenc($url)),
			));
		$chk = \A11yc\Util::remove_query_strings($chk, array('a11yc_pages'));
	?>
	<tr>
		<th scope="row" style="word-break: break-all;"><?php echo $page_title.'<br /><a href="'.$url.'">'.$url.'</a>' ?></th>
		<td class="a11yc_result"><?php echo \A11YC\Evaluate::result_str($v['level'], $setup['target_level']) ?></td>
		<td class="a11yc_result"><a href="<?php echo $chk ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<td class="a11yc_result" style="white-space: nowrap;"><?php echo \A11yc\Util::s($v['date']) ?></td>
	</tr>
	<?php endforeach; ?>
	</table>
<?php
endforeach;
?>
<?php
else:
	echo A11YC_LANG_PAGES_NOT_FOUND;
endif;

// related page
include (__DIR__.'/inc_related.php');
?>
