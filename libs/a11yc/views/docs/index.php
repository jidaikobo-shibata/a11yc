<h2><?php echo A11YC_LANG_DOCS_TEST ?></h2>
<ul>
<?php foreach ($test['tests'] as $code => $v): ?>
	<li><a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>"><?php echo $v['name'] ?></a></li>
<?php endforeach; ?>
</ul>

<!-- show technique index -->
<?php /* ?>
<div id="a11yc_header">
	<!-- a11yc menu -->
	<ul id="a11yc_menu_principles">
	<?php foreach ($yml['principles'] as $v):  ?>
		<li id="a11yc_menuitem_<?php echo $v['code'] ?>"><a href="#a11yc_header_p_<?php echo $v['code'] ?>"><?php echo $v['code'].' '.$v['name'] ?></a></li>
	<?php endforeach;  ?>
	</ul><!--/#a11yc_menu_principles-->
</div><!--/#a11yc_header-->
<?php */ ?>
<?php foreach ($yml['principles'] as $k => $v): ?>

	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	 foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue; ?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><h3 class="a11yc_header_guideline a11yc_disclosure"><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<div class="a11yc_section_criterions a11yc_disclosure_target">
		<?php foreach ($yml['criterions'] as $kkk => $vvv):
			if (substr($kkk, 0, 3) != $kk) continue; ?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion" data-a11yc-lebel="l_<?php echo strtolower($vvv['level']['name']) ?>">
			<h4 class="a11yc_header_criterion a11yc_disclosure"><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')' ?></h4>
			<ul class="a11yc_outlink">
			<?php if (isset($vvv['url_as'])):  ?>
				<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
			<?php endif;  ?>
				<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
			</ul>
			<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>
			
			<!-- checks -->
			<ul class="a11yc_ul_check a11yc_disclosure_target show">
			<?php foreach ($yml['checks'][$kkk] as $code => $val):
				$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : ''; ?>
				<li<?php echo $non_interference ?>>
				<a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>"><?php echo $val['name'] ?></a></li>
			<?php endforeach; ?>
			</ul>
			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!-- /.a11yc_section_criterions.a11yc_disclosure_target -->
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?> a11yc_section_principle-->
<?php endforeach; ?>
