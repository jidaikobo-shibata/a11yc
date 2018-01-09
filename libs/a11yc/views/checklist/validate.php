<?php
namespace A11yc;
// call from post
$is_call_from_post = isset($is_call_from_post);

if (empty($errs['errors'])):
	echo '<p id="a11yc_validation_not_found_error"><span class="a11yc_icon_fa" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif;

if ($errs['errors'] || $errs['notices']):
// error
	$html = '';
	$class_str = $is_call_from_post ? '' : ' a11yc_disclosure';
	$html.= $is_call_from_post ? '<p class="' : '<h1 class="a11yc_resetstyle ';
	$html.='a11yc_narrow_level'.$class_str.'" data-a11yc-narrow-target="#a11yc_validation_list">';
	$html.= $is_call_from_post ? '' : A11YC_LANG_CHECKLIST_MACHINE_CHECK;

	//narrow level
	foreach ($errs_cnts as $lv => $errs_cnt):
		$level  = $lv=='total' ? '"l_a","l_aa","l_aaa"' : '"l_'.$lv.'"';
		$class_str = $lv == 'total' ? ' current' : '';
		$html.='<a role="button" class="a11yc_resetstyle'.$class_str.'" tabindex="0" data-narrow-level=\'['.$level .']\'><span class="a11yc_errs_lv">'.strtoupper($lv).'</span> <span class="a11yc_errs_cnt">'.intval($errs_cnt).'</span></a> ';
	endforeach;

	$html.= $is_call_from_post ? '</p>' : '</h1>';

	echo $html;
	$class_str = $is_call_from_post ? '' : 'a11yc_disclosure_target a11yc_hide_if_fixedheader hide';
	?>
	<div class="<?php echo $class_str ?>">
		<div id="a11yc_validation_errors" class="">
			<div class="a11yc_controller">
			</div>
			<?php if ( ! empty($errs['errors'])): ?>
			<dl id="a11yc_validation_list">
			<?php foreach ($errs['errors'] as $err): ?>
				<?php echo $err ?>
			<?php endforeach; ?>
			</dl>
			<?php endif; ?>

			<?php
			// notices
			if ( ! empty($errs['notices'])):
			?>
				<dl id="a11yc_validation_notices_list">
				<?php foreach ($errs['notices'] as $err): ?>
					<?php echo $err ?>
				<?php endforeach; ?>
				</dl>
			<?php
			endif;
			?>

		</div><!-- /#a11yc_validation_errors -->

		<dl id="a11yc_validation_code">
			<dt>
<?php
			$class_str = $is_call_from_post ? '' : ' a11yc_disclosure';
?>
				<a role="button" class="a11yc_resetstyle<?php echo $class_str ?>" tabindex="0"><?php echo A11YC_LANG_CHECKLIST_SOURCE ?></a>
			</dt>

			<dd class="a11yc_disclosure_target show">
				<div class="a11yc_controller"></div>
				<div class="a11yc_source">
					<?php if (strpos($raw, '===a11yc_rplc===') !== false):
						echo A11YC_LANG_CHECKLIST_COULD_NOT_DRAW_HTML;
					else: ?>
					<div id="a11yc_validation_code_raw">
						<?php echo $raw ?>
					</div>
					<?php /* ?>
					<div id="a11yc_validation_code_txt" style="display: none;">
						<?php echo $raw ?>
					</div>
					<?php */ ?>
					<?php endif; ?>
				</div><!-- /.a11yc_disclosure_target -->
			</dd>
		</dl>
</div><!-- /.a11yc_disclosure_target -->
<?php
endif;
?>
