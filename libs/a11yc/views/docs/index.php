<div id="a11yc_header_ctrl">
<?php /* ?>
	<!-- narrow level -->
	<p class="a11yc_narrow_level a11yc_hide_if_no_js" data-a11yc-narrow-target="#a11yc_docs">
	<?php
		echo '<a role="button" tabindex="0" data-narrow-level="l_a,l_aa,l_aaa" class="current">'.A11YC_LANG_DOCS_ALL.'</a>';
		for ($i=1; $i<=3; $i++)
		{
			echo '<a role="button" tabindex="0" data-narrow-level="'.implode(',', array_slice(array('l_a', 'l_aa', 'l_aaa'), 0, $i)).'"'.$class_str.'>'.\A11yc\Util::num2str($i).'</a>';
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
$class_str_show = $word && $word!='' ? ' show' : '';

$html = '';
foreach ($test['tests'] as $code => $v):
	if ($word && ! \A11yc\Arr::get($results, 'tests')) continue;
	if ($word && ! in_array($code, $results['tests'])) continue;
	\A11yc\View::assign('doc', $v, false);
	$html.= '<li class="a11yc_disclosure_parent"><a role="button" class="a11yc_disclosure">'.$v['name'].'</a>';
	$html.= '<div class="a11yc_disclosure_target'.$class_str_show.'">';
	$html.= \A11yc\View::fetch_tpl('docs/each.php');
	$html.= '</div>';
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
if ($word && ! \A11yc\Arr::get($results, 'chks')) continue;
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
		<div class="a11yc_section_criterions a11yc_disclosure_target<?php echo $class_str_show ?>">
		<?php foreach ($yml['criterions'] as $kkk => $vvv):
		if ($word && ! in_array($kkk, $results['chks']['criterions'])) continue;
			if (substr($kkk, 0, 3) != $kk) continue;

			$non_interference = isset($vvv['non-interference']) ? '&nbsp;'.A11YC_LANG_CHECKLIST_NON_INTERFERENCE :'';
			$class_str = isset($vvv['non-interference']) ? ' non_interference' : '';
			$class_str.= ' a11yc_criterion_l_'.strtolower($vvv['level']['name']);
			 ?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion<?php echo $class_str ?> a11yc_level_<?php echo strtolower($vvv['level']['name']) ?>" data-a11yc-lebel="l_<?php echo strtolower($vvv['level']['name']) ?>">
			<h4 class="a11yc_header_criterion a11yc_disclosure"><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' <span class="a11yc_header_criterion_level">('.$vvv['level']['name'].$non_interference.')</span>' ?></h4>
			<ul class="a11yc_outlink">
			<?php if (isset($vvv['url_as'])): ?>
				<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
			<?php endif; ?>
				<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
			</ul>
			<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>

			<!-- checks -->
			<ul class="a11yc_ul_check a11yc_disclosure_target show">
			<?php foreach ($yml['checks'][$kkk] as $code => $val):
				if ($word && ! in_array($code, $results['chks']['codes'])) continue;
				$non_interference = isset($vvvv['non-interference']) ? ' non_interference" title="non interference"' : ''; ?>
				<li class="a11yc_disclosure_parent<?php echo $non_interference ?>">
				<a role="button" class="a11yc_disclosure" <?php /* echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>"<?php */ ?>><?php echo $val['name'] ?></a>
				<div class="a11yc_section_each_docs a11yc_disclosure_target<?php echo $class_str_show ?>">
					<?php
						\A11yc\View::assign('is_call_form_index', true);
						\A11yc\View::assign('doc', $val, false);
						echo \A11yc\View::fetch_tpl('docs/each.php');
					?>
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