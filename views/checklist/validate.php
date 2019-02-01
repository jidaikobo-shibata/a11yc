<?php
namespace A11yc;

$machine_check_status  = Values::machineCheckStatus();

// call from post
$is_call_from_post = isset($is_call_from_post);

if (empty($errs['errors'])):
	echo '<p id="a11yc_validation_not_found_error"><span class="a11yc_icon_fa" role="presentation" aria-hidden="true"></span>'.A11YC_LANG_CHECKLIST_NOT_FOUND_ERR.'</p>';
endif;

if (Arr::get($errs, 'errors') || Arr::get($errs, 'notices')):
// error
	$html = '';
	if ($is_call_from_post):
		$html.='<p class="a11yc_narrow_level" data-a11yc-narrow-target="#a11yc_validation_list">';
	else:
		$html.= '<details><summary>';
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
	<div id="a11yc_validator_results">
		<div class="hide">
			<div id="a11yc_validation_errors" class="">
				<?php if ( ! empty($errs['errors'])): ?>
				<h3 class="a11yc_heading a11yc_erros_heading">Error</h3>
				<dl id="a11yc_validation_error_list" class="a11yc_validation_list">
				<?php
					foreach ($errs['errors'] as $k => $err):
						if (isset($err['dt'])):
							echo $err['dt'];
						endif;

						echo isset($err['li']) && ! empty($err['li']) ? $err['li'] : '';

						$next = $k + 1;
						if (isset($errs['errors'][$next]['dt'])):
							echo '</ul></dd>';
						endif;
					endforeach;
				?>
				</ul></dd></dl>
				<?php endif; ?>

				<?php
				// notices
				if ( ! empty($errs['notices'])):
				?>
					<h3 class="a11yc_heading a11yc_notice_heading">Notice</h3>
					<dl id="a11yc_validation_notices_list" class="a11yc_validation_list">
					<?php
						foreach ($errs['notices'] as $k => $err):
							if (isset($err['dt'])):
								echo $err['dt'];
							endif;

							echo Arr::get($err, 'li', '');

							$next = $k + 1;
							if (isset($errs['notices'][$next]['dt'])):
								echo '</ul></dd>';
							endif;
						endforeach;
					?>
					</ul></dd></dl>
				<?php endif; ?>
			</div><!-- /#a11yc_validation_errors -->

			<?php if ($is_call_from_post): ?>
			<dl id="a11yc_validation_code">
				<dt><?php echo A11YC_LANG_CHECKLIST_SOURCE ?></dt>
				<dd>
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
		<?php endif; ?>
		</div><!--  /.hide -->
	</div><!-- /#a11yc_validator_results -->
<?php if ($is_call_from_post === false): ?>
</details>
<?php endif; ?>


	<?php
	// logs
	if ( ! empty($logs)):
		echo '<details>';
		echo '<summary>'.A11YC_LANG_CHECKLIST_MACHINE_CHECK_LOG.'</summary>';
	?>
		<?php
			foreach ($logs as $err => $log):
			echo '<details>';
			echo '<summary>'.$yml['errors'][$err]['title'].'</summary>';
			echo '<ul>';
				foreach ($log as $loghtml => $logresult):
					if ($logresult == 0) continue;
					if (
						$logresult == 4 || // not exist
						$loghtml == A11YC_LANG_CHECKLIST_MACHINE_CHECK_UNSPEC // place not specified
					):
						echo '<li>'.$machine_check_status[$logresult].'</li>';
					else:
						echo '<li>'.$machine_check_status[$logresult].': '.Util::s($loghtml).'</li>';
					endif;

				endforeach;
				echo '</ul>';
			echo '</details>';
			endforeach;
		?>
	<?php
		echo '</details>';
		endif; // logs
	?>
<?php
endif;
?>
