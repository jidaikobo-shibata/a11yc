<?php namespace A11yc; ?>
<h2><?php echo $title ?></h2>
<?php
if ($pages):
foreach ($pages as $k => $each_pages):
	if (empty($each_pages)) continue;
	if ($k == 'pdfs'):
		echo '<h3>PDF</h3>';
	else:
		echo '<h3>'.$selection_reasons[$k].'</h3>';
	endif;
?>
	<table class="a11yc_table a11yc_report">
	<thead>
	<tr>
		<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
		<?php if ( ! Arr::get($settings, 'show_url_results')): ?>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></th>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_TEST_DATE ?></th>
		<?php endif; ?>
	</tr>
	</thead>

	<?php
	$site_level = Evaluate::checkSiteLevel();
	foreach ($each_pages as $v):
		// alternative content
		$alt_url = '';
		if ( ! empty($v['alt_url'])):
			$alt_url = '<div class="a11yc_results_alt_url">'.sprintf(A11YC_LANG_ALT_URL_LEVEL, Util::s(Util::urldec($v['alt_url']))).'</div>';
		endif;

		$url = Util::s(Util::urldec($v['url']));
		$page_title = Util::s($v['title']);
		$chk = Util::addQueryStrings(
			Util::uri(),
			array(
				array('url', Util::urlenc($url)),
			));
		$chk = Util::removeQueryStrings($chk, array('a11yc_pages'));
	?>
	<tr>
		<th scope="row" style="word-break: break-all;">
			<?php
			echo $page_title;
			if ( ! Arr::get($settings, 'show_url_results')):
				echo '<br /><a href="'.$url.'">'.$url.'</a>';
			endif;
			?>
		</th>
		<td class="a11yc_result"><?php echo Evaluate::resultStr($v['level'], $settings['target_level']).$alt_url ?></td>
		<?php if ( ! Arr::get($settings, 'show_url_results')): ?>
		<td class="a11yc_result"><a href="<?php echo $chk ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
		<td class="a11yc_result" style="white-space: nowrap;"><?php echo Util::s($v['date']) ?></td>
		<?php endif; ?>
	</tr>
	<?php endforeach; ?>
	</table>
<?php
endforeach;
?>
<?php
else:
	echo A11YC_LANG_PAGE_NOT_FOUND;
endif;

// related page
include (__DIR__.'/inc_related.php');
?>
