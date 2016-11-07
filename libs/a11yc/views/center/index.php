<h2><?php echo A11YC_LANG_TEST_RESULT ?></h2>
<table class="a11yc_table">
<tbody>

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

	<!-- selected method -->
	<tr>
		<th><?php echo A11YC_LANG_CANDIDATES_TITLE ?></th>
		<td>
		<?php
		$arr = array(
			A11YC_LANG_CANDIDATES1,
			A11YC_LANG_CANDIDATES2,
			A11YC_LANG_CANDIDATES3,
			A11YC_LANG_CANDIDATES4,
		);
		echo $arr[$selected_method]
		?>
		</td>
	</tr>
	<!-- /selected method -->

	<!-- number of checked -->
	<?php if (isset($done)): ?>
	<tr>
		<th><?php echo A11YC_LANG_NUM_OF_CHECKED ?></th>
		<td><?php echo $done['done'].' / '.$total['total'] ?></td>
	</tr>
	<?php endif; ?>
	<!-- /number of checked -->

	<!-- unpassed pages -->
	<?php if (isset($unpassed_pages)): ?>
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
			<?php elseif (count($passed_pages) >= 1): ?>
			<?php echo A11YC_LANG_UNPASSED_PAGES_NO ?>
			<?php else: ?>
			<?php echo '-' ?>
		<?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
	<!-- /unpassed pages -->

</table>

<!-- site results -->
<?php if ($result): ?>
<h2><?php echo A11YC_LANG_CHECKLIST_TITLE ?></h2>
<?php echo $result ?>
<?php endif; ?>

<!-- Bookmarklet -->
<h2>Bookmarklet</h2>

<?php echo A11YC_LANG_CENTER_BOOKMARKLET_EXP; ?>

<p><a href='javascript:(function(){var%20a11yc_pass,url;a11yc_pass="<?php echo A11YC_CHECKLIST_URL; ?>";url=encodeURI(location.href);window.document.location=a11yc_pass+url;})();'>A11yc checker</a></p>

<textarea style="width:100%;height:8.25em;">
javascript:(function(){
	var a11yc_pass,url;
	a11yc_pass="<?php echo A11YC_CHECKLIST_URL; ?>";
	url=encodeURI(location.href);
	window.document.location=a11yc_pass+url;
})();
</textarea>
<!-- /Bookmarklet -->

<div id="a11yc_center_about" class="a11yc_cmt">
<h2><?php echo A11YC_LANG_CENTER_ABOUT ?></h2>
<img src="<?php echo A11YC_VALIDATE_URL ?>/libs/a11yc/img/logo_author.png" id="a11yc_logo_author" alt="<?php echo A11YC_LANG_CENTER_LOGO ?>">
<p><?php echo A11YC_LANG_CENTER_ABOUT_CONTENT ?></p>
<div><!-- /.a11yc_cmt -->
