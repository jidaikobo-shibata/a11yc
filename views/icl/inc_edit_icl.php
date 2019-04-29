<?php namespace A11yc; ?>

<table class="a11yc_table">

<tr>
	<th><label for="a11yc_title_short"><?php echo A11YC_LANG_NAME ?></label></th>
	<td>
		<textarea id="a11yc_title_short" name="title_short" style="width: 100%;" rows="7"><?php echo Arr::get($item, 'title_short', '') ?></textarea><br />
	</td>
</tr>

<tr>
	<th><label for="a11yc_title"><?php echo A11YC_LANG_ICL_IMPLEMENT ?></label></th>
	<td>
		<textarea id="a11yc_title" name="title" style="width: 100%;" rows="7"><?php echo Arr::get($item, 'title', '') ?></textarea><br />
	</td>
</tr>

<tr>
	<th><label for="a11yc_situation"><?php echo A11YC_LANG_SITUATION ?></label></th>
	<td>
		<select id="a11yc_situation" name="situation" style="width:100%">
			<option data-criterion="" value="0"></option>
		<?php
			foreach (Model\Icl::fetchAll(true, true) as $id => $v):
				if ($v['is_sit'] === false) continue;
				$selected = $id == Arr::get($item, 'situation', '') ? ' selected="selected"' : '' ;
			?>
				<option<?php echo $selected ?> data-criterion="<?php echo $v['criterion'] ?>" value="<?php echo $id ?>"><?php echo $v['title'].' - '.Util::key2code($v['criterion']) ?></option>
		<?php
			endforeach;
		?>
		</select>
	</td>
</tr>

<tr>
	<th><label for="a11yc_identifier"><?php echo A11YC_LANG_ICL_ID ?></label></th>
	<td>
		<input type="text" id="a11yc_identifier" name="identifier" style="width: 100%;" value="<?php echo Arr::get($item, 'identifier', '') ?>" />
	</td>
</tr>

<tr>
	<th><label for="a11yc_inspection"><?php echo A11YC_LANG_ICL_VALIDATE ?></label></th>
	<td>
		<textarea id="a11yc_inspection" name="inspection" style="width: 100%;" rows="7"><?php echo Arr::get($item, 'inspection', '') ?></textarea>
	</td>
</tr>


<tr>
	<th><label for="a11yc_seq"><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></label></th>
	<td>
		<input type="text" id="a11yc_seq" name="seq" style="width: 3em;" value="<?php echo intval(Arr::get($item, 'seq', 0)) ?>" />
	</td>
</tr>

<?php echo View::fetchTpl('inc_implements.php') ?>

</table>

<?php if ($is_add): ?>
<input type="hidden" name="is_add" value="1" />
<?php endif; ?>
