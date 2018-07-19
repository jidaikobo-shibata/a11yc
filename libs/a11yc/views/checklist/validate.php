<?php
namespace A11yc;
// call from post
$is_call_from_post = isset($is_call_from_post);
//$is_call_from_post = false;

if (empty($errs['errors'])):
	echo '<p id="a11yc_validation_not_found_error" class="a11yc_hide_if_fixedheader"><span class="a11yc_icon_fa" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif;

if ($errs['errors'] || $errs['notices']):
// error
	$html = '';
	if ($is_call_from_post):
		$html.='<p class="a11yc_narrow_level a11yc_hide_if_fixedheader" data-a11yc-narrow-target="#a11yc_validation_list">';
	else:
		$html.= '<details class="a11yc_hide_if_fixedheader"><summary>';
		$html.='<h1 class="a11yc_heading a11yc_resetstyle a11yc_narrow_level" data-a11yc-narrow-target="#a11yc_validation_list">';
		$html.= A11YC_LANG_CHECKLIST_MACHINE_CHECK;
	endif;

	//narrow level
	foreach ($errs_cnts as $lv => $errs_cnt):
		$level  = $lv=='total' ? '"l_a","l_aa","l_aaa"' : '"l_'.$lv.'"';
		$class_str = $lv == 'total' ? ' current' : '';
		$html.='<a role="button" class="a11yc_resetstyle'.$class_str.'" tabindex="0" data-narrow-level=\'['.$level .']\'><span class="a11yc_errs_lv">'.strtoupper($lv).'</span> <span class="a11yc_errs_cnt">'.intval($errs_cnt).'</span></a> ';
	endforeach;

	// number of notice and error
	$html.= '<span class="a11yc_notice_cnt">Notice '.count($errs['notices']).'</span>';

	$html.= $is_call_from_post ? '</p>' : '</h1></summary>';

	echo $html;
	?>
	<div id="_a11yc_validator_results">

	<div class="a11yc_hide_if_fixedheader hide">
		<div id="a11yc_validation_errors" class="">
			<div class="a11yc_controller">
			</div><!-- /.a11yc_controller -->
			<?php if ( ! empty($errs['errors'])): ?>
			<dl id="a11yc_validation_error_list" class="a11yc_validation_list">
			<?php foreach ($errs['errors'] as $err): ?>
				<?php echo $err ?>
			<?php endforeach; ?>
			</dl>
			<?php endif; ?>

			<?php
			// notices
			if ( ! empty($errs['notices'])):
			?>
				<dl id="a11yc_validation_notices_list" class="a11yc_validation_list">
				<?php foreach ($errs['notices'] as $err): ?>
					<?php echo $err ?>
				<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div><!-- /#a11yc_validation_errors -->

		<?php if ($is_call_from_post): ?>
		<dl id="a11yc_validation_code">
			<dt><?php echo A11YC_LANG_CHECKLIST_SOURCE ?></dt>
			<dd>
		<?php else: ?>
		<details id="a11yc_validation_code">
			<summary><?php echo A11YC_LANG_CHECKLIST_SOURCE ?></summary>
			<div class="a11yc_controller"></div>
		<?php endif; ?>
			<div class="a11yc_source">
				<?php if (strpos($raw, '===a11yc_rplc===') !== false):
					echo A11YC_LANG_CHECKLIST_COULD_NOT_DRAW_HTML;
				else: ?>
				<div id="a11yc_validation_code_raw">
					<?php echo $raw ?>
				</div><!-- /#a11yc_validation_code_raw -->
				<?php /* ?>
				<div id="a11yc_validation_code_txt" style="display: none;">
					<?php echo $raw ?>
				</div>
				<?php */ ?>
				<?php endif; ?>
			</div><!-- /.a11yc_source -->
	<?php if ($is_call_from_post): ?>
			</dd>
		</dl>
	</div>
	<?php else: ?>
		</details>
	</details>
	<?php endif; ?>


	<?php
	// logs
	if ( ! empty($logs)):
//		echo '<details>';
		echo '<summary>'.A11YC_LANG_CHECKLIST_MACHINE_CHECK_LOG.'</summary>';
	?>
		<dl id="a11yc_validation_logs_list" class="a11yc_validation_list">
		<?php foreach ($logs as $err => $log): ?>
			<dt><?php echo $yml['errors'][$err]['title'] ?></dt>
			<?php
				foreach ($log as $loghtml => $logresult):
					if ($logresult == 0) continue;
					if (
						$logresult == 4 || // not exist
						$loghtml == A11YC_LANG_CHECKLIST_MACHINE_CHECK_UNSPEC // place not specified
					):
			?>
				<dd><?php echo $machine_check_status[$logresult] ?></dd>
			<?php
					else:
			?>
				<dd><?php echo $machine_check_status[$logresult] ?>: <?php echo mb_substr(Util::s($loghtml), 0, 100).'...' ?></dd>
			<?php
					endif;
			?>

			<?php endforeach; ?>
		<?php endforeach; ?>
		</dl>
	<?php
//		echo '</details>';
		endif;
	?>

</div><!-- /#a11yc_validator_results -->
<?php
endif;
?>
