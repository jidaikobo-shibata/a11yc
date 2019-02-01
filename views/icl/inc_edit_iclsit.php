<?php namespace A11yc; ?>

<table class="a11yc_table">

<tr>
	<th><label for="a11yc_title"><?php echo A11YC_LANG_ICL_IMPLEMENT ?></label></th>
	<td>
		<textarea id="a11yc_title" name="title" style="width: 100%;" rows="7"><?php echo Arr::get($item, 'title', '') ?></textarea><br />
	</td>
</tr>

<tr>
	<th><label for="a11yc_seq"><?php echo A11YC_LANG_CTRL_ORDER_SEQ ?></label></th>
	<td>
		<input type="text" id="a11yc_seq" name="seq" style="width: 3em;" value="<?php echo intval(Arr::get($item, 'seq', 0)) ?>" />
	</td>
</tr>

<?php echo View::fetchTpl('inc_criterions.php') ?>

</table>

<?php if ($is_add): ?>
<input type="hidden" name="is_add" value="1" />
<?php endif; ?>
