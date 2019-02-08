<?php namespace A11yc; ?>
<?php include('inc_submenu.php'); ?>

<table class="a11yc_table">

<tr>
	<th><?php echo A11YC_LANG_NAME ?></th>
	<td><?php echo Arr::get($item, 'title_short', '') ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ICL_IMPLEMENT ?></th>
	<td><?php echo Arr::get($item, 'title', '') ?></td>
</tr>

<?php
		$situations = Model\Icl::fetchAll();
		$situation = Arr::get($item, 'situation', '');
		if (isset($situations[$situation]['title'])):
?>
<tr>
	<th><?php echo A11YC_LANG_SITUATION ?></th>
	<td><?php
		echo $situations[$situation]['title'];
	?></td>
</tr>
<?php endif; ?>

<tr>
	<th><?php echo A11YC_LANG_ICL_ID ?></th>
	<td><?php echo Arr::get($item, 'identifier', '') ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ICL_VALIDATE ?></th>
	<td><?php echo Arr::get($item, 'inspection', '') ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></th>
	<td><?php echo intval(Arr::get($item, 'seq', 0)) ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ICL_ID ?></th>
	<td><?php echo Arr::get($item, 'identifier', '') ?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_CRITERION ?></th>
	<td><?php
		$criterions = Yaml::each('criterions');
		$criterion = Arr::get($item, 'criterion', '');
		echo $criterions[$criterion]['name'];
	?></td>
</tr>

<tr>
	<th><?php echo A11YC_LANG_ICL_RELATED ?></th>
	<td><?php
		$html = '';
		foreach (Yaml::each('techs') as $tech => $v):
			if ( ! in_array($tech, Arr::get($item, 'techs', array()))) continue;
			$html.= '<li>'.$v['title'].'</li>';
		endforeach;
		echo empty($html) ? '' : '<ul>'.$html.'</ul>' ;
	?></td>
</tr>

</table>
