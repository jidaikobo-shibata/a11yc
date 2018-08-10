<?php
namespace A11yc;
$html = '';

// loop
foreach ($yml['criterions'] as $criterion => $v):
	$criterion_code = Util::key2code($criterion);
	if (
		// ordinary condition
		($include && strlen($v['level']['name']) <= $target_level) ||
		// additional condition
		(
			! $include && strlen($v['level']['name']) > $target_level &&
			in_array($criterion, Values::additionalCriterions())
		)
	):

	// strs
	$exist_str = Arr::get($results, "{$criterion}.non_exist") == 1 ? A11YC_LANG_EXIST_NON : A11YC_LANG_EXIST;
	$pass_str = Arr::get($results, "{$criterion}.passed") >= 1 ? A11YC_LANG_PASS : A11YC_LANG_PASS_NON;
	$memo = Util::s(Arr::get($results, "{$criterion}.memo", ''));
	$alert_class = $pass_str == '-' ? ' class="a11yc_not_passed"' : '';

	$non_interference = isset($v['non-interference']) && $v['non-interference'] ?
										' <strong>('.A11YC_LANG_CHECKLIST_NON_INTERFERENCE.')</strong>' :
										'';

	// html
	$html.= '<tr'.$alert_class.'>';
	$html.= '	<th scope="row" class="a11yc_result">'.$criterion_code.'</th>';
	$html.= '	<td class="a11yc_result">'.$v['name'].$non_interference.'</td>';
	$html.= '	<td class="a11yc_result">'.$v['level']['name'].'</td>';
	$html.= '	<td class="a11yc_result a11yc_result_exist">'.$exist_str.'</td>';
	$html.= '	<td class="a11yc_result">'.$pass_str.'</td>';

	if ( ! $is_total):
		$chks = array('t' => array(), 'f' => array());
		foreach (array_keys($chks) as $type):
			if ( ! isset($yml['techs_codes'][$criterion][$type])) continue;
			foreach ($yml['techs_codes'][$criterion][$type] as $code):
				if ( ! is_array($cs)) continue;
				if ( ! array_key_exists($code, $cs)) continue;
				$chks[$type][] = '<li><a href="'.A11YC_REF_WCAG20_TECH_URL.$code.'.html">'.$yml['techs'][$code]['title'].'</a></li>';
			endforeach;
		endforeach;

		// checklist
		$html.= '	<td class="a11yc_result">';
		$html.= $chks['t'] ? '<ul>'.join("\n", $chks['t']).'</ul>' : '';
		$html.= '</td>';

		// failure
		$html.= '	<td class="a11yc_result">';
		$html.= $chks['f'] ? '<ul>'.join("\n", $chks['f']).'</ul>' : '';
		$html.= '</td>';
	endif; // is_total

	// memo or percentage
	$html.= $is_total ? '	<td class="a11yc_result">' : '	<td>';
	$html.= nl2br($memo);
	$html.= '	</td>';

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
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_PASS ?></th>
			<?php if ( ! $is_total): ?>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_TITLE ?></th>
			<th scope="col" class="a11yc_result"><?php echo A11YC_LANG_CHECKLIST_NG_REASON ?></th>
			<?php endif; ?>
			<th scope="col" class="a11yc_result">
			<?php
				if (Input::get('url')):
					echo A11YC_LANG_CHECKLIST_MEMO;
				else:
					echo A11YC_LANG_CHECKLIST_PERCENTAGE;
				endif;
			?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php echo $html; ?>
	</tbody>
</table><!--/.a11yc_table-->
<?php
endif;
?>
