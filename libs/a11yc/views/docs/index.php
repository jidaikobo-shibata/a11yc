<form action="<?php echo A11YC_URL ?>" method="GET">
<input type="hidden" name="c" value="docs">
<input type="hidden" name="a" value="index">
<label class="a11yc_skip" for="a11yc_str"><?php echo A11YC_LANG_DOCS_SEARCH ?></label><input type="text" name="s" id="a11yc_str" size="40" value="<?php echo $word ?>">
<input type="submit" value="<?php echo A11YC_LANG_DOCS_SEARCH ?>">
</form>


<?php
$msg = A11YC_LANG_DOCS_SEARCH_RESULT_NONE;
$html = '';
foreach ($test['tests'] as $code => $v):
	if ($word && ! in_array($code, $results['tests'])) continue;
//	$html.= '<li><a'.A11YC_TARGET.' href="'.A11YC_DOC_URL.$code.'">'.$v['name'].'</a></li>';
	$html.= '<li><a role="button" class="a11yc_disclosure">'.$v['name'].'</a>';
	$html.= '<div class="a11yc_disclosure_target"></div>';
	$html.= '</li>';
endforeach;

if ($html):
$msg = '';
?>
<h2><?php echo A11YC_LANG_DOCS_TEST ?></h2>
<ul class="a11yc_ul_test">
	 <?php echo $html ?>
</ul>
<?php endif; ?>

<!-- show technique index -->
<?php
foreach ($yml['principles'] as $k => $v):
if ($word && ! in_array($k, $results['chks']['principles'])) continue;
$msg = '';
?>

	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	 foreach ($yml['guidelines'] as $kk => $vv):
		if ($word && ! in_array($kk, $results['chks']['guidelines'])) continue;
		if ($kk{0} != $k) continue; ?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><h3 class="a11yc_header_guideline a11yc_disclosure"><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<?php $class_str = $word ? ' show' : ''; ?>
		<div class="a11yc_section_criterions a11yc_disclosure_target<?php echo $class_str ?>">
		<?php foreach ($yml['criterions'] as $kkk => $vvv):
		if ($word && ! in_array($kkk, $results['chks']['criterions'])) continue;
			if (substr($kkk, 0, 3) != $kk) continue;

			$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
//			$skip_non_interference = isset($vvv['non-interference']) ? '<span class="a11yc_skip">&nbsp;('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</span>' : '';
			$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
			$class_str.= ' a11yc_criterion_l_'.strtolower($vvv['level']['name']);
			 ?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion<?php echo $class_str ?>" data-a11yc-lebel="l_<?php echo strtolower($vvv['level']['name']) ?>">
			<h4 class="a11yc_header_criterion a11yc_disclosure"><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' <span class="a11yc_header_criterion_level">('.$vvv['level']['name'].$non_interference.')</span>' ?></h4>
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
				if ($word && ! in_array($code, $results['chks']['codes'])) continue;
				$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : ''; ?>
				<li<?php echo $non_interference ?>>
				<a role="button" class="a11yc_disclosure" <?php /* echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>"<?php */ ?>><?php echo $val['name'] ?></a>
				<div class="a11yc_section_each_docs a11yc_disclosure_target">
				</div>
				</li>
			<?php endforeach; ?>
			</ul>
			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!-- /.a11yc_section_criterions.a11yc_disclosure_target -->
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?> a11yc_section_principle-->
<?php endforeach; ?>

<?php echo $msg ?>