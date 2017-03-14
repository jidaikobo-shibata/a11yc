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
			in_array($criterion, Controller_Setup::additional_criterions())
		)
	):

	// strs
	$exist_str = Arr::get($results, "{$criterion}.non_exist") == 1 ? A11YC_LANG_EXIST_NON : A11YC_LANG_EXIST;
	$pass_str = Arr::get($results, "{$criterion}.passed") >= 1 ? A11YC_LANG_PASS : '-';
	$memo = Util::s(Arr::get($results, "{$criterion}.memo", ''));

	// html
	$html.= '<tr>';
	$html.= '	<th scope="row">'.$criterion_code.'</th>';
	$html.= '	<td>'.$v['name'].'</td>';
	$html.= '	<td class="a11yc_result">'.$v['level']['name'].'</td>';
	$html.= '	<td class="a11yc_result a11yc_result_exist">'.$exist_str.'</td>';
	$html.= '	<td class="a11yc_result">'.$pass_str.'</td>';

	// memo or percentage
	$html.= $is_total ? '	<td class="a11yc_result">' : '	<td>';
	$html.= $memo;
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
