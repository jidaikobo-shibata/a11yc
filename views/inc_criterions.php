<?php namespace A11yc; ?>

<tr>
	<th><label for="a11yc_criterion"><?php echo A11YC_LANG_CRITERION ?></label></th>
	<td>
		<select id="a11yc_criterion" name="criterion">
			<option value="0">-</option>
		<?php
			foreach (Yaml::each('criterions') as $k => $v):
				$selected = $k == Arr::get($item, 'criterion', Input::get('criterion')) ?
									' selected="selected"' :
									'' ;
		?>
			<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo Util::key2code($k).' '.$v['name'] ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
