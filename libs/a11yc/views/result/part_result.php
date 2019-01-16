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
	$html.= '	<th scope="row" class="a11yc_result  a11yc_result_code">'.$criterion_code.'</th>';
	$html.= '	<td class="a11yc_result a11yc_result_string">'.$v['name'].$non_interference.'</td>';
	$html.= '	<td class="a11yc_result">'.$v['level']['name'].'</td>';
	$html.= '	<td class="a11yc_result a11yc_result_exist">'.$exist_str.'</td>';
	if ( ! $is_policy):
		$html.= '	<td class="a11yc_result">'.$pass_str.'</td>';

		// memo or percentage
		$html.= $is_total ? '	<td class="a11yc_result">' : '	<td>';
		$html.= nl2br($memo);
		$html.= '	</td>';
	endif; // is_policy

	$html.= '</tr>';
	endif;
endforeach;

// render
if ($html):
?>
<table class="a11yc_table">
	<thead>
		<tr>
			<th scope="col" colspan="2"><?php echo A11YC_LANG_CRITERION ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_LEVEL ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_EXIST ?></th>
			<?php if ( ! $is_policy):  ?>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PASS ?></th>
			<th scope="col" class="a11yc_result">
			<?php
				if (Input::get('url')):
					echo A11YC_LANG_CHECKLIST_MEMO;
				else:
					echo A11YC_LANG_CHECKLIST_PERCENTAGE;
				endif;
			?>
			</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php echo $html; ?>
	</tbody>
</table><!--/.a11yc_table-->
<?php
endif;
?>
