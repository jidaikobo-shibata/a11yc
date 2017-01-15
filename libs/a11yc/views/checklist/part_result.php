<?php
$html = '';

// additional criterions
$str = str_replace('&quot;', '"', $setup['additional_criterions']);
$additional_criterions = $str ? unserialize($str) : array();

// total
$is_total = ! \A11yc\Input::get('url');

// loop
foreach ($yml['criterions'] as $k => $v):
	$criterion_code = \A11yc\Util::key2code($k);
	if (
		// ordinary condition
		($include && strlen($v['level']['name']) <= $target_level) ||
		// additional condition
		(
			! $include && strlen($v['level']['name']) > $target_level &&
			in_array($k, $additional_criterions)
		)
	):

	// strs
	$is_passed = $results[$k]['pass'];
	$memo = \A11yc\Util::s($results[$k]['memo']);
	$pass_str = $is_passed ? A11YC_LANG_PASS : '-';

	// exist_str
	$exist_str = '-';
	if ( ! $is_total && isset($results[$k]['non_exist']))
	{
		$exist_str = A11YC_LANG_EXIST_NON;
	}
	elseif (
		$is_passed || // individual
		($is_total && $memo) // total
		)
	{
		// at the total and it has memo (percentage), then it tells us to it is existed.
		$exist_str = A11YC_LANG_EXIST;
	}

	// individual: memo
	// total: percentage
	// if blank memo and this page was total. this criterion is passed by upper criterion
	$memo = $is_total && $is_passed && ! $memo ? '100%' : $memo;

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
				if (\A11yc\Input::get('url')):
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
