<?php namespace A11yc; ?>

<tr>
	<th><label for="a11yc_criterion"><?php echo A11YC_LANG_CRITERION ?></label></th>
	<td>
		<ul style="column-count: 3; column-gap: 26px; list-style: none; margin: 0; padding: 0;">
		<?php
			foreach (Yaml::each('criterions') as $k => $v):
				$checked = in_array($k, Arr::get($item, 'criterions', array(Input::get('criterion')))) ?
									' checked="checked"' :
									'' ;
		?>
			<li><label><input type="checkbox"<?php echo $checked ?> name="criterions[]" value="<?php echo $k ?>"><?php echo Util::key2code($k).' '.$v['name'] ?></label></li>
		<?php endforeach; ?>
		</ul>
	</td>
</tr>
