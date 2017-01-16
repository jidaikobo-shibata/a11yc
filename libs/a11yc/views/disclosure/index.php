<h2><?php echo $title ?></h2>

<?php
// report
if ($is_total):
	echo $setup['report'];
endif;
?>

<table class="a11yc_table">

	<!-- target level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><?php echo \A11yc\Util::num2str($target_level) ?></td>
	</tr>
	<!-- /target level -->

	<!-- current total level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td>
		<?php
		$site_level = \A11yc\Evaluate::check_site_level();
		echo \A11yc\Evaluate::result_str($site_level, $target_level);
		?>
		</td>
	</tr>
	<!-- /current total level -->

	<?php if ( ! $is_total): ?>
	<!-- current level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
		<td>
		<?php
		echo \A11yc\Evaluate::result_str($page['level'], $target_level);
		?>
		</td>
	</tr>
	<!-- /current level -->
	<?php endif; ?>

	<?php if ($is_total && $setup['dependencies']): ?>
	<!-- dependencies -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_DEPENDENCIES ?></th>
		<td><?php echo $setup['dependencies']; ?></td>
	</tr>
	<!-- /dependencies -->
	<?php endif; ?>

	<!-- selected method -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
		<td>
		<?php
		if ($is_total):
			echo $selected_methods[$selected_method];
			if ( ! $is_center):
				echo ' (<a href="'.$pages_link.'">'.A11YC_LANG_CHECKED_PAGES.'</a>)';
			endif;
		else:
			echo $selection_reasons[$page['selection_reason']];
		endif;
		?>
		</td>
	</tr>
	<!-- /selected method -->

	<?php if (isset($page) && \A11yc\Arr::get($page, 'url')): ?>
	<!-- target page -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_PAGES_URLS ?></th>
		<td><?php
			echo '<a href="'.$page['url'].'">'.$page['url'].'</a>';
			if (\Kontiki\Auth::auth()):
				echo ' <a href="'.A11YC_CHECKLIST_URL.\A11yc\Util::urlenc($page['url']).'"'.A11YC_TARGET.' class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>';
			endif;
		?></td>
	</tr>
	<!-- /target page -->
	<?php endif; ?>

	<!-- period of date -->
	<tr>
	<?php if ($is_total): ?>
		<th scope="row"><?php echo A11YC_LANG_TEST_PERIOD ?></th>
		<td><?php echo $setup['test_period'] ?></td>
	<?php else: ?>
		<th scope="row"><?php echo A11YC_LANG_TEST_DATE ?></th>
		<td><?php echo $page['date'] ?></td>
	<?php endif; ?>
	</tr>
	<!-- /period of date -->

	<!-- number of checked -->
	<?php if (isset($done) && $is_total): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><?php echo $done['num'].' / '.$total['num'] ?></td>
	</tr>
	<?php endif; ?>
	<!-- /number of checked -->

	<!-- unpassed pages -->
	<?php if (isset($unpassed_pages) && $is_total): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
		<?php if ($unpassed_pages): ?>
			<ul>
			<?php
			foreach ($unpassed_pages as $v):
				$url = s($v['url']);
			?>
				<li>
					<a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $url ?></a>
					<?php
					if (\Kontiki\Auth::auth()):
						echo ' <a href="'.A11YC_CHECKLIST_URL.\A11yc\Util::urlenc($url).'"'.A11YC_TARGET.' class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_PAGES_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>';
					endif;
					?>
				</li>
			<?php endforeach; ?>
			</ul>
			<?php

			elseif (count($passed_pages) >= 1):
				echo A11YC_LANG_UNPASSED_PAGES_NO;
			else:
				echo '-';
			endif;
			?>
		</td>
	</tr>
	<?php endif; ?>
	<!-- /unpassed pages -->

	<!-- contact -->
	<?php if ($setup['contact']): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CONTACT ?></th>
		<td><?php echo $setup['contact']; ?></td>
	</tr>
	<?php endif; ?>
	<!-- /contact -->

</table>

<!-- site results -->
<?php if (isset($result) && $result): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php
echo $result;
endif;
if (isset($additional) && $additional): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL ?></h2>
<?php
echo $additional;
endif;

// related page
include (__DIR__.'/inc_related.php');
?>
