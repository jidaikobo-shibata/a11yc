<?php namespace A11yc; ?>

<?php if ( ! $is_assign): ?>
<h2><?php echo $title ?> <?php if (Arr::get($settings, 'declare_date') && Arr::get($settings, 'declare_date') != '0000-00-00'): echo '('.$settings['declare_date'].')'; endif;?></h2>
<?php endif; ?>

<table class="a11yc_table a11yc_table_report">

	<!-- standard -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_STANDARD_REPORT ?></th>
		<td><?php
			 $standards = Yaml::each('standards');
			 echo $standards[Arr::get($settings, 'standard', 0)];
		?></td>
	</tr>
	<!-- /standard -->

	<!-- target level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_TARGET_LEVEL ?></th>
		<td><?php echo Util::num2str(Arr::get($settings, 'target_level')) ?></td>
	</tr>
	<!-- /target level -->

	<!-- current total level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CURRENT_LEVEL_WEBPAGES ?></th>
		<td><?php echo $site_level.$site_alt_exception; ?></td>
	</tr>
	<!-- /current total level -->

	<?php if ( ! $is_total): ?>
	<!-- current level -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CURRENT_LEVEL ?></th>
		<td><?php echo $level.$alt_results ?>
		</td>
	</tr>
	<!-- /current level -->
	<?php endif; ?>

	<?php if ($is_total && Arr::get($settings, 'dependencies')): ?>
	<!-- dependencies -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_DEPENDENCIES ?></th>
		<td><?php echo $settings['dependencies']; ?></td>
	</tr>
	<!-- /dependencies -->
	<?php
		endif;

		if ($is_total):
		$selected_methods = Values::selectedMethods();
	?>
	<!-- dependencies -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
		<td><?php echo $selected_methods[Arr::get($settings, 'selected_method', 0)]; ?></td>
	</tr>
	<!-- /dependencies -->
	<?php
	endif;

	if (isset($page) && Arr::get($page, 'selection_reason') != '0'): ?>
	<!-- selected method -->
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
		<td><?php
			$selection_reasons = Values::selectionReasons();
			echo Arr::get($selection_reasons, $page['selection_reason']);
		?></td>
	</tr>
	<!-- /selected method -->
	<?php endif; ?>

	<!-- number of checked -->
	<?php if ( ! $is_assign && isset($done) && $is_total && ! Arr::get($settings, 'hide_url_results')): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CHECKED_PAGES_URLS ?></th>
		<td><?php
			echo '<a href="'.$pages_link.'">'.A11YC_LANG_CHECKED_PAGES.'</a>';
			echo ' ('.$done.' / '.$total.')';
		?></td>
	</tr>
	<?php endif; ?>
	<!-- /number of checked -->

	<!-- unpassed pages -->
	<?php if ( ! $is_assign && isset($unpassed_pages) && $is_total): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_UNPASSED_PAGES ?></th>
		<td>
		<?php if ($unpassed_pages): ?>
			<ul>
			<?php
			foreach ($unpassed_pages as $v):
				$url = Util::s($v['url']);
			?>
				<li>
					<?php if (Arr::get($settings, 'hide_url_results')): ?>
						<?php echo $v['title'] ?>
					<?php else: ?>
						<a href="<?php echo $url ?>"<?php echo A11YC_TARGET ?>><?php echo $v['title'] ?></a>
					<?php endif; ?>
					<?php if ( ! $is_assign && ! $is_download): ?>
						(<a href="<?php echo $chk_link ?>&amp;url=<?php echo Util::urlenc($url) ?>"><?php echo A11YC_LANG_TEST_RESULT ?></a>)
					<?php

					if (Auth::auth()):
						echo ' <a href="'.A11YC_CHECKLIST_URL.Util::urlenc($url).'"'.A11YC_TARGET.' class="a11yc_hasicon"><span class="a11yc_skip"><?php echo A11YC_LANG_CTRL_CHECK ?></span><span class="a11yc_icon_check a11yc_icon_fa" role="presentation" aria-hidden="true"></span></a>';
					endif;
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

	<!-- period of date -->
	<tr>
	<?php if ( ! $is_assign && $is_total): ?>
		<th scope="row"><?php echo A11YC_LANG_TEST_PERIOD ?></th>
		<td><?php echo Arr::get($settings, 'test_period', '') ?></td>
	<?php elseif ( ! $is_assign): ?>
		<th scope="row"><?php echo A11YC_LANG_TEST_DATE ?></th>
		<td><?php echo $page['date'] ?></td>
	<?php endif; ?>
	</tr>
	<!-- /period of date -->

	<!-- contact -->
	<?php if (Arr::get($settings, 'contact', false)): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CONTACT ?></th>
		<td><?php echo $settings['contact']; ?></td>
	</tr>
	<?php endif; ?>
	<!-- /contact -->

</table>

<!-- report -->
<?php if ($is_total && Arr::get($settings, 'report', false)): ?>
<h2><?php echo A11YC_LANG_OPINION ?></h2>
<?php echo htmlspecialchars_decode($settings['report']); ?>
<?php endif; ?>
<!-- /report -->

<!-- results -->
<?php
include('inc_criterions_checklist.php');
include('inc_implements_checklist.php');

// related page
if ( ! $is_center) include (__DIR__.'/inc_related.php');
?>
