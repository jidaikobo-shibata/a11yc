<?php
namespace A11yc;
$html = '';
$is_policy = isset($is_policy) ? $is_policy : false;

// loop
foreach ($yml['criterions'] as $criterion => $v):
	$criterion_code = Util::key2code($criterion);
	if (
		// ordinary condition
		($include && strlen($v['level']['name']) <= $target_level) ||
		// additional condition
		(
			! $include && strlen($v['level']['name']) > $target_level &&
			in_array($criterion, Model\Setting::fetch('additional_criterions'))
		)
	):

	// strs
	$exist_str = A11YC_LANG_EXIST;
	if (
		in_array($criterion, $non_exist_and_passed_criterions) ||
		Arr::get($results, "{$criterion}.non_exist") == 1
	):
		$exist_str = A11YC_LANG_EXIST_NON;
	endif;

	$pass_str = A11YC_LANG_PASS_NON;

	if (
		in_array($criterion, $non_exist_and_passed_criterions) ||
		Arr::get($results, "{$criterion}.passed") >= 1
	):
		$pass_str = A11YC_LANG_PASS;
	endif;

	$memo = Util::s(Arr::get($results, "{$criterion}.memo", ''));
	$alert_class = $pass_str == '-' ? ' class="a11yc_not_passed"' : '';

	$non_interference = isset($v['non-interference']) && $v['non-interference'] ?
										' <strong>('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</strong>' :
										'';

	// html
	$html.= '<tr'.$alert_class.'>';
	$html.= '	<th scope="row" class="a11yc_result a11yc_result_string">'.$criterion_code;
	$html.= '&nbsp;'.$v['name'].$non_interference.'</td>';
	$html.= '	<td class="a11yc_result a11yc_level">'.$v['level']['name'].'</td>';
	$html.= '	<td class="a11yc_result a11yc_result_exist">'.$exist_str.'</td>';
	if ( ! $is_policy):
		$html.= '	<td class="a11yc_result a11yc_pass_str">'.$pass_str.'</td>';

		// memo or percentage
		if ($is_total || ! Model\Setting::fetch('hide_memo_results')):
			$html.= $is_total ? '	<td class="a11yc_result">' : '	<td class="a11yc_memo">';
			$html.= nl2br($memo);
			$html.= '	</td>';
		endif;
	endif; // is_policy

	if ( ! $is_total && ! Model\Setting::fetch('hide_failure_results')):
		$lis = '';
		foreach (Arr::get($cs, $criterion, array()) as $tech):
			if (is_numeric($tech)) continue;
			$lis.= '<li>'.$tech.'</li>';
		endforeach;
		$ul = empty($lis) ? '' : '<ul>'.$lis.'</ul>';
		$html.= '	<td class="a11yc_result a11yc_result_exist">'.$ul.'</td>';

		$html.= '</tr>';
		endif;
	endif;
endforeach;

// render
if ($html):
?>
<table class="a11yc_table">
	<thead>
		<tr>
			<th scope="col"><?php echo A11YC_LANG_CRITERION ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
			<th scope="col" class="a11yc_result a11yc_result_exist"><?php echo A11YC_LANG_EXIST ?></th>
			<?php if ( ! $is_policy):  ?>
			<th scope="col" class="a11yc_result a11yc_result_exist"><?php echo A11YC_LANG_PASS ?></th>
			<?php if ($is_total || ! Model\Setting::fetch('hide_memo_results')):  ?>

			<th scope="col" class="a11yc_result">
			<?php
				if ($is_total):
					echo A11YC_LANG_CHECKLIST_PERCENTAGE;
				else:
					echo A11YC_LANG_CHECKLIST_MEMO;
				endif;
			?>
			</th>

			<?php endif; ?>
			<?php endif; ?>
			<?php if ( ! $is_total && ! Model\Setting::fetch('hide_failure_results')):  ?>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_NG_REASON ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php echo $html; ?>
	</tbody>
</table><!--/.a11yc_table-->
<?php
endif;
