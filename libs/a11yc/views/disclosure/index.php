<h2><?php echo $title ?></h2>

<?php
// report
if ($is_total):
	echo $setup['report'];
endif;
?>

<table class="a11yc_table">

	<!-- Accessibility Policy -->
	<tr>
		<th><?php echo A11YC_LANG_POLICY ?></th>
		<td><?php echo '<p class="a11yc_link"><a href="'.\A11yc\Util::add_query_strings(\A11yc\Util::uri(), array(array('a11yc_policy', 1))).'">'.A11YC_LANG_POLICY.'</a></p>'; ?></td>
	</tr>
	<!-- /Accessibility Policy -->

	<?php if ($setup['selected_method'] !== 0 && $is_total == FALSE):  ?>
	<!-- link to report -->
	<tr>
		<th><?php echo A11YC_LANG_REPORT ?></th>
		<td><?php echo '<p class="a11yc_link"><a href="'.\A11yc\Util::remove_query_strings(\A11yc\Util::uri(), array('url', 'a11yc_pages')).'">'.A11YC_LANG_REPORT.'</a></p>'; ?></td>
	</tr>
	<!-- /link to report -->
	<?php endif;  ?>

	<!-- target level -->
	<tr>
		<th><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><?php echo \A11YC\Util::num2str($target_level) ?></td>
	</tr>
	<!-- /target level -->

	<!-- current level -->
	<tr>
		<th><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td>
		<?php
		$site_level = \A11YC\Evaluate::check_site_level();
		echo \A11YC\Evaluate::result_str($site_level, $target_level);
		?>
		</td>
	</tr>
	<!-- /current level -->

	<?php if ($is_total): ?>
	<!-- dependencies -->
	<tr>
		<th><?php echo A11YC_LANG_DEPENDENCIES ?></th>
		<td><?php echo $setup['dependencies']; ?></td>
	</tr>
	<!-- /dependencies -->

	<!-- selected method -->
	<tr>
		<th><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
		<td>
		<?php
		$arr = array(
			A11YC_LANG_CANDIDATES0,
			A11YC_LANG_CANDIDATES1,
			A11YC_LANG_CANDIDATES2,
			A11YC_LANG_CANDIDATES3,
			A11YC_LANG_CANDIDATES4,
		);
		echo $arr[$selected_method];
		echo ' (<a href="'.\A11yc\Util::add_query_strings(\A11yc\Util::uri(), array(array('a11yc_pages', 1))).'">'.A11YC_LANG_CHECKED_PAGES.'</a>)'
		?>
		</td>
	</tr>
	<!-- /selected method -->
	<?php else: ?>

	<!-- target page -->
	<tr>
		<th><?php echo A11YC_LANG_PAGES_URLS ?></th>
		<td><?php echo '<a href="'.$page['url'].'">'.$page['url'].'</a>' ?></td>
	</tr>
	<!-- /target page -->

	<?php endif; ?>

	<!-- period or date -->
	<tr>
	<?php if ($is_total): ?>
		<th><?php echo A11YC_LANG_TEST_PERIOD ?></th>
		<td><?php echo $setup['test_period'] ?></td>
	<?php else: ?>
		<th><?php echo A11YC_LANG_TEST_DATE ?></th>
		<td><?php echo $page['date'] ?></td>
	<?php endif; ?>
	</tr>
	<!-- /period or date -->

	<!-- number of checked -->
	<?php if (isset($done) && $is_total): ?>
	<tr>
		<th><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><?php echo $done['done'].' / '.$total['total'] ?></td>
	</tr>
	<?php endif; ?>
	<!-- /number of checked -->

	<!-- unpassed pages -->
	<?php if (isset($unpassed_pages) && $is_total): ?>
	<tr>
		<th><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
		<?php if ($unpassed_pages): ?>
			<ul>
			<?php
			foreach ($unpassed_pages as $v):
				$url = s($v['url']);
			?>
				<li>
					<a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $url ?></a>
					(<a href="<?php echo A11YC_CHECKLIST_URL.$url ?>"<?php echo A11YC_TARGET ?>>check</a>)
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
		<th><?php echo A11YC_LANG_CONTACT ?></th>
		<td><?php echo $setup['contact']; ?></td>
	</tr>
	<?php endif; ?>
	<!-- /contact -->

</table>

<!-- site results -->
<?php if (isset($result) && $result): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>
<?php endif; ?>

<?php if (isset($additional) && $additional): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL ?></h2>
<?php echo $additional ?>
<?php endif; ?>
