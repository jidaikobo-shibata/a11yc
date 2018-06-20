<?php namespace A11yc; ?>
<div id="a11yc_header_ctrl">
<?php /* ?>
	<!-- narrow level -->
	<p class="a11yc_narrow_level a11yc_hide_if_no_js" data-a11yc-narrow-target="#a11yc_docs">
	<?php
		echo '<a role="button" tabindex="0" data-narrow-level="l_a,l_aa,l_aaa" class="current">'.A11YC_LANG_DOCS_ALL.'</a>';
		for ($i=1; $i<=3; $i++)
		{
			echo '<a role="button" tabindex="0" data-narrow-level="'.implode(',', array_slice(array('l_a', 'l_aa', 'l_aaa'), 0, $i)).'"'.$class_str.'>'.Util::num2str($i).'</a>';
		}
	?>
</p>
<?php */ ?>
	<!-- search form -->
	<?php echo $search_form; ?>
</div><!--#a11yc_header_ctrl-->
<div class="a11yc_presentation" role="presentation"></div>
<?php
$msg = A11YC_LANG_DOCS_SEARCH_RESULT_NONE;
$is_disclosure_open = $word && $word!='' ? ' open' : '';

$html = '';
foreach ($tests as $code => $v):
	if ($word && ! Arr::get($results, 'tests')) continue;
	if ($word && ! in_array($code, $results['tests'])) continue;
	View::assign('doc', $v, false);
	$html.= '<li><a href="'.$a11yc_doc_url.$code.'">'.$v['name'].'</a></li>';
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
if ($word && ! Arr::get($results, 'criterions')) continue;
if ($word && ! in_array($k, $results['criterions']['principles'])) continue;
$msg = '';
?>

	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	 foreach ($yml['guidelines'] as $kk => $vv):
		if ($word && ! in_array($kk, $results['criterions']['guidelines'])) continue;
		if ($kk{0} != $k) continue; ?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><details<?php echo $is_disclosure_open ?>><summary><h3 class="a11yc_header_guideline a11yc_heading"><?php echo Util::key2code($vv['code']).' '.$vv['name'] ?></h3></summary>

		<!-- criterions -->
		<div class="a11yc_section_criterions">
		<?php foreach ($yml['criterions'] as $kkk => $vvv):
		if ($word && ! in_array($kkk, $results['criterions']['criterions'])) continue;
			if (substr($kkk, 0, 3) != $kk) continue;

			$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
			$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
			$class_str.= ' a11yc_criterion_l_'.strtolower($vvv['level']['name']);
			 ?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion<?php echo $class_str ?> a11yc_level_<?php echo strtolower($vvv['level']['name']) ?>" data-a11yc-lebel="l_<?php echo strtolower($vvv['level']['name']) ?>">
			<h4 class="a11yc_header_criterion"><a href="<?php echo $a11yc_doc_url.$vvv['code'] ?>"><?php echo Util::key2code($vvv['code']).' '.$vvv['name'].' <span class="a11yc_header_criterion_level">('.$vvv['level']['name'].$non_interference.')</span>' ?></a></h4>
			<ul class="a11yc_outlink">
			<?php if (isset($vvv['url_as'])): ?>
				<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
			<?php endif; ?>
				<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
			</ul>
			<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>

			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach; ?>
		</div><!-- /.a11yc_section_criterions -->
		</details>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach; ?>
	</div><!--/#section_p_<?php echo $v['code'] ?> a11yc_section_principle-->
<?php endforeach; ?>

<?php echo $msg ?>