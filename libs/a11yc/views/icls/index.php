<?php namespace A11yc; ?>

<?php echo $submenu ?>

<?php
function a11yc_gen_icls_table($vals, $situation_id = 0)
{
	$html = '';
	$html.= '<table class="a11yc_table">';
	foreach ($vals as $k => $v):
		if ($v['situation'] != $situation_id) continue;
		$html.= '<tr>';
		$html.= '<td><input type="checkbox" name="str" id="str" value=""></td>';
		$html.= '<td>'.$v['title'].'</td>';
		$html.= '<td>'.$v['identifier'].'</td>';
		$html.= '<td>';
		if (is_array($v['implements'])):
			$html.= '<ul>';
			foreach ($v['implements'] as $vv):
				$html.= '<li>'.$vv.'</li>';
			endforeach;
			$html.= '</ul>';
		endif;
		$html.= '</td>';
		$html.= '<td>'.$v['inspection'].'</td>';
		$html.= '</tr>';
	endforeach;
	$html.= '</table>';
	return $html;
}

$html = '';
$html.= '<form action="'.A11YC_ICLS_URL.'index" method="POST">';
foreach ($yml['criterions'] as $i):
	$code = Util::key2code($i['code']);
	if (empty($icls[$code])) continue;
	$html.= '<h2>'.$code.'</h2>';
	if (empty($iclssit[$code])):
		$html.= a11yc_gen_icls_table($icls[$code]);
	else:
		foreach ($iclssit[$code] as $k => $v):
			$html.= '<h3>'.$v['value'].'</h3>';
			$html.= a11yc_gen_icls_table($icls[$code], $v['id']);
		endforeach;
	endif;
endforeach;

$html.= '<div id="a11yc_submit">';
$html.= '<input type="submit" value="'.A11YC_LANG_CTRL_SEND.'">';
$html.= '</div>';

$html.= '</form>';

echo $html;
?>
