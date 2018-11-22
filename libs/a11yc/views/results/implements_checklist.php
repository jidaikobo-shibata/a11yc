<?php namespace A11yc; ?>
<h2><?php echo A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE ?></h2>

<?php
foreach ($techs_codes as $criterion_code => $implement):
	if (empty($implement)) continue;
	if ( ! in_array($criterion_code, Values::targetCriterions())) continue;
?>
<h3><?php echo Util::key2code($criterion_code).' '.$criterions[$criterion_code]['name'] ?></h3>

<table class="a11yc_table_check">
<thead>
<tr>
	<th>ID</th>
	<th style="width: 30%;"><?php echo A11YC_LANG_CHECKLIST_IMPLEMENTSLIST_TITLE ?></th>
	<th style="width: 30%;"><?php echo A11YC_LANG_TEST_METHOD_AC_AF ?></th>
	<th><?php echo A11YC_LANG_EXIST ?></th>
	<th><?php echo A11YC_LANG_PASS ?></th>
	<th><?php echo A11YC_LANG_TEST_METHOD ?></th>
	<th><?php echo A11YC_LANG_CHECKLIST_MEMO ?></th>
</tr>
</thead>
<?php
foreach ($implement as $i):
?>
<tr>
	<th><?php echo $criterion_code.'_'.$i ?></th>
	<th><?php echo $techs[$i]['title'] ?></th>
	<td>
	<?php
	$is_af_exist = false;
	$is_ac_exist = false;
	foreach (Arr::get($implements, $criterion_code, array()) as $k => $tech):
		if (in_array($i, Arr::get($tech, 'techs'))):
			if (Arr::get($errors[$k], 'notice') === true):
				$is_af_exist = true;
			endif;
			if ( ! Arr::get($errors[$k], 'notice')):
				$is_ac_exist = true;
			endif;
			echo $implements[$criterion_code][$k]['title'].'<br>';
		endif;
	endforeach;
	?>
	</td>
	<td></td>
	<td></td>
	<td>
	<?php
	$ways = array();
	if ($is_af_exist || $is_ac_exist):
		if ($is_ac_exist):
			$ways[] = 'AC';
		endif;
		if ($is_ac_exist || $is_af_exist):
			$ways[] = 'AF';
		endif;
	else:
		$ways[] = 'HC';
	endif;
	echo join('/', $ways);
	?>
	</td>
	<td></td>
</tr>
<?php endforeach; ?>
</table>
<?php endforeach; ?>
