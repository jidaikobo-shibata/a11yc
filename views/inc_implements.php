<?php namespace A11yc; ?>

<?php include('inc_criterions.php'); ?>

<tr>
	<th><?php echo A11YC_LANG_ICL_RELATED ?></th>
	<td>
	<?php
	$html = '';
	foreach (Yaml::each('techs') as $tech => $v):
		$criterions = json_encode(array_map('trim', $v['apps']));
		$checked = in_array($tech, Arr::get($item, 'techs', array())) ? ' checked="checked"' : '';
		$html.= '<label style="display: block" class="a11yc_implement_item" data-criterions=\''.$criterions.'\'>';
		$html.= '<input type="checkbox"'.$checked.' name="techs[]" value="'.$tech.'">';
		$html.= $v['title'];
		$html.= '</label>';
	endforeach;
	echo $html;
	?>
	</td>
</tr>
