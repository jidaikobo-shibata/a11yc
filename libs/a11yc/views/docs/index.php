<h2><?php echo A11YC_LANG_DOCS_TEST ?></h2>
<ul>
<?php foreach ($test['tests'] as $code => $v): ?>
	<li><a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>"><?php echo $v['name'] ?></a></li>
<?php endforeach; ?>
</ul>

<!-- show technique index -->
<?php foreach ($yml['principles'] as $k => $v): ?>

	<!-- principles -->
	<div id="section_p_<?php echo $v['code'] ?>" class="section_guidelines"><h2 id="p_<?php echo $v['code'] ?>" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	 foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue; ?>
		<div id="g_<?php echo $vv['code'] ?>" class="section_guideline"><h3><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<div class="section_criterions">
		<?php foreach ($yml['criterions'] as $kkk => $vvv):
			if (substr($kkk, 0, 3) != $kk) continue; ?>
			<div id="c_<?php echo $kkk ?>" class="section_criterion l_<?php echo strtolower($vvv['level']['name']) ?>">
			<div class="a11yc_criterion">
			<h4><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')' ?>
			<?php if (isset($vvv['url_as'])): ?>
				<a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" class="link_as">Accessibility Supported</a>
			<?php endif; ?>
			<a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" class="link_understanding">Understanding</a></h4>
			<p><?php echo $vvv['summary'] ?></p></div><!-- /.a11yc_criterion -->

			<!-- checks -->
			<ul>
			<?php foreach ($yml['checks'][$kkk] as $code => $val):
				$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : ''; ?>
				<li<?php echo $non_interference ?>>
				<a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>"><?php echo $val['name'] ?></a></li>
			<?php endforeach; ?>
			</ul>
			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!--/.section_criterions-->
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?> section_guidelines-->
<?php endforeach; ?>
