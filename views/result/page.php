<?php namespace A11yc; ?>
<?php
if ($pages):
foreach ($pages as $k => $each_pages):
	if (empty($each_pages)) continue;
	if ($k == 'pdfs'):
		echo '<h2>PDF</h2>';
	else:
		echo '<h2>'.Arr::get(Values::selectionReasons(), $k).'</h2>';
	endif;
?>
	<table class="a11yc_table a11yc_report">
	<thead>
	<tr>
		<th scope="col"><?php echo A11YC_LANG_CHECKLIST_TARGETPAGE ?></th>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
		<?php if ( ! Arr::get($settings, 'hide_url_results')): ?>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_TEST_RESULT ?></th>
		<th class="a11yc_result" scope="col"><?php echo A11YC_LANG_TEST_DATE ?></th>
		<?php endif; ?>
	</tr>
	</thead>

	<?php
	foreach ($each_pages as $v):
		// alternative content
		$alt_url = '';
		if ( ! empty($v['alt_url'])):
			$alt_url = '<div class="a11yc_results_alt_url">'.sprintf(A11YC_LANG_ALT_URL_LEVEL, Util::s(Util::urldec($v['alt_url']))).'</div>';
		endif;

		$url = Util::s(Util::urldec($v['url']));
	?>
	<tr>
		<th scope="row" style="word-break: break-all;">
			<?php
			echo Util::s($v['title']);
			if ( ! Arr::get($settings, 'hide_url_results')):
				echo '<br /><a href="'.$url.'">'.$url.'</a>';
			endif;
			?>
		</th>

		<td class="a11yc_result">
		<?php
			echo Evaluate::resultStr($v['level'], $settings['target_level']).$alt_url ;
		?>
		</td>

		<?php if ( ! Arr::get($settings, 'hide_url_results')): ?>
		<td class="a11yc_result"><a href="<?php echo $chk_link ?>&amp;url=<?php echo Util::s(Util::urlenc($url)) ?>" class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_TEST_RESULT ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a></td>
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
if ( ! $is_center) include (__DIR__.'/inc_related.php');
?>
