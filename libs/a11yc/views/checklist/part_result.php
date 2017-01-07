<?php
$html = '';

// additional criterions
$str = str_replace('&quot;', '"', $setup['additional_criterions']);
$additional_criterions = $str ? unserialize($str) : array();

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
	$non_exist = isset($results[$k]['non_exist']) ? A11YC_LANG_EXIST_NON : A11YC_LANG_EXIST;
	$pass_str = $results[$k]['pass'] ? A11YC_LANG_PASS : '-';

	// html
	$html.= '<tr>';
	$html.= '	<th scope="row">'.$criterion_code.'</th>';
	$html.= '	<td>'.$v['name'].'</td>';
	$html.= '	<td class="a11yc_result">'.$v['level']['name'].'</td>';
	$html.= '	<td class="a11yc_result a11yc_result_exist">';
	if ($results[$k]['pass']):
		$html.= $non_exist;
	else:
		$html.= '-';
	endif;
	$html.= '	</td>';
	$html.= '	<td class="a11yc_result">';
	$html.= $pass_str;
	$html.= '	</td>';

	// individual: memo
	// total: percentage
	$html.= \A11yc\Input::get('url') ? '	<td>' : '	<td class="a11yc_result">';
	$html.= \A11yc\Util::s($results[$k]['memo']);
	// if blank memo and this page was total. this criterion is passed by upper criterion
	if ( ! \A11yc\Input::get('url') && ! $results[$k]['memo'] && $results[$k]['pass']):
		$html.= '100%';
	endif;
	$html.= '	</td>';
	$html.= '</tr>';
	endif;
endforeach;

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
