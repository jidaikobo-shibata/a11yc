<div id="a11yc_checks" data-a11yc-current-user="<?php echo $current_user_id ?>">

<p id="a11yc_narrow_level" class="a11yc_hide_if_no_js">Level: 
<?php 
	for ($i=1; $i<=3; $i++)
	{
		$class_str = $i == $target_level ? ' class="current"' : '';
		echo '<a role="button" tabindex="0" data-narrow-level="'.implode(',', array_slice(array('l_a', 'l_aa', 'l_aaa'), 0, $i)).'"'.$class_str.'>'.str_repeat('A', $i).'</a>';
	}
?>
</p>

<!-- header -->
<div id="a11yc_header">

<!-- rest of num -->
<table id="a11yc_rest">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th scope="col">A</th>
			<th scope="col">AA</th>
			<th scope="col">AAA</th>
			<th scope="col">total</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4" scope="row"><a role="button" class="a11yc_disclosure"><?php echo A11YC_LANG_CHECKLIST_RESTOFNUM ?></a></th>
			<td class="a11yc_rest_total">&nbsp;-&nbsp;</td>
		</tr>
	</tfoot>
	<tbody class="a11yc_disclosure_target">
<?php foreach ($yml['principles'] as $v): ?>
		<tr id="a11yc_rest_<?php echo $v['code'] ?>">
			<th scope="row"><?php echo $v['code'].'&nbsp;'.$v['name'] ?></th>
			<td data-a11yc-rest-lebel="a">&nbsp;-&nbsp;</td>
			<td data-a11yc-rest-lebel="aa">&nbsp;-&nbsp;</td>
			<td data-a11yc-rest-lebel="aaa">&nbsp;-&nbsp;</td>
			<td class="a11yc_rest_subtotal">&nbsp;-&nbsp;</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<!-- not for bulk -->
<?php if ($url != 'bulk'):  ?>
	<!-- back to target page -->
	<p><a href="<?php echo s(urldecode($url)) ?>">back to target page</a></p>

	<!-- level -->
	<p><?php echo A11YC_LANG_TARGET_LEVEL ?>: <?php echo \A11yc\Util::num2str($target_level) ?></p>
	<?php $current_level = $target_level ? \A11yc\Evaluate::result_str($page['level'], $target_level) : '-';  ?>
	<p><?php echo A11YC_LANG_CURRENT_LEVEL ?>: <?php echo $current_level ?></p>

	<!-- standard -->
	<p><label for="a11yc_standard"><?php echo A11YC_LANG_STANDARD ?></label>
	<select name="standard" id="a11yc_standard">
	<?php
	foreach ($standards['standards'] as $k => $v):
		$selected = $k == $page['standard'] ? ' selected="selected"' : '';
	?>
		<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
	<?php endforeach;  ?>
	</select></p>
	<?php
		// is done
		$checked = @$page['done'] ? ' checked="checked"' : '';
	?>
	<p><label for="a11yc_done"><?php echo A11YC_LANG_CHECKLIST_DONE ?>: <input type="checkbox" name="done" id="a11yc_done" value="1"<?php echo $checked ?> /></label></p>

	<?php if ($errs): ?>
		<ul id="a11yc_errors" style="height: 200px; overflow: auto; border: 1px #aaa solid;">
		<?php foreach ($errs as $err):  ?>
			<li><?php echo $err ?></li>
		<?php endforeach;  ?>
		</ul>
	<?php else:
		echo A11YC_LANG_CHECKLIST_NOT_FOUND_ERR;
	endif; ?>
<?php else:  ?>
	<div><label for="a11yc_update_all"><?php echo A11YC_LANG_BULK_UPDATE ?></label>
	<select name="update_all" id="a11yc_update_all">
		<option value="1"><?php echo A11YC_LANG_BULK_UPDATE1 ?></option>
		<option value="2"><?php echo A11YC_LANG_BULK_UPDATE2 ?></option>
		<option value="3"><?php echo A11YC_LANG_BULK_UPDATE3 ?></option>
	</select></div>

	<div><label for="a11yc_update_done"><?php echo A11YC_LANG_BULK_DONE ?></label>
	<select name="update_done" id="a11yc_update_done">
		<option value="1"><?php echo A11YC_LANG_BULK_DONE1 ?></option>
		<option value="2"><?php echo A11YC_LANG_BULK_DONE2 ?></option>
		<option value="3"><?php echo A11YC_LANG_BULK_DONE3 ?></option>
	</select></div>
<?php endif;  ?>

	<!-- menu -->
	<ul id="a11yc_menu_principles">
	<?php foreach ($yml['principles'] as $v):  ?>
		<li id="a11yc_menuitem_<?php echo $v['code'] ?>"><a href="#p_<?php echo $v['code'] ?>"><?php echo $v['code'].' '.$v['name'] ?></a></li>
	<?php endforeach;  ?>
	</ul><!--/#a11yc_menu_principles-->

</div><!--/#a11yc_header-->

<?php foreach ($yml['principles'] as $k => $v):  ?>
	<!-- principles -->
	<div id="a11yc_p_<?php echo $v['code'] ?>" class="a11yc_section_principle"><h2 id="a11yc_header_p_<?php echo $v['code'] ?>" class="a11yc_header_principle" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue;
	?>
		<div id="a11yc_g_<?php echo $vv['code'] ?>" class="a11yc_section_guideline"><h3 class="a11yc_header_guideline"><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<?php
		foreach ($yml['criterions'] as $kkk => $vvv):
			if (substr($kkk, 0, 3) != $kk) continue;
		?>
			<div id="a11yc_c_<?php echo $kkk ?>" class="a11yc_section_criterion" data-a11yc-lebel="l_<?php echo strtolower($vvv['level']['name']) ?>">
			<h4 class="a11yc_header_criterion"><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')' ?></h4>
			<ul class="a11yc_outlink">
			<?php if (isset($vvv['url_as'])):  ?>
				<li class="a11yc_outlink_as"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" title="Accessibility Supported"><span class="a11yc_skip">Accessibility Supported</span></a></li>
			<?php endif;  ?>
				<li class="a11yc_outlink_u"><a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" title="Understanding"><span class="a11yc_skip">Understanding</span></a></li>
			</ul>
			<p class="summary_criterion"><?php echo $vvv['summary'] ?></p>

			<!-- checks -->
			<table class="a11yc_table_check"><tbody>
			<?php
			foreach ($yml['checks'][$kkk] as $code => $val):
				$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : '';
				$passes = array();
				if (isset($val['pass'])):
					foreach ($val['pass'] as $pass_code => $pass_each):
						$passes = array_merge($passes, $pass_each);
					endforeach;
				endif;
				$data = $passes ? ' data-pass="'.join(',', $passes).'"' : '';

				$checked = '';
				if (
					($page && isset($cs[$code])) ||
					( ! $page && isset($bulk[$code]))
				):
					$checked = ' checked="checked"';
				endif;
			?>

				<tr<?php echo $non_interference ?>>

				<th>
				<label for="<?php echo $code ?>"><input type="checkbox"<?php echo $checked ?> id="<?php echo $code ?>" name="chk[<?php echo $code ?>][on]" value="1" <?php echo $data ?> class="<?php echo $vvv['level']['name'] ?>" /><?php echo $val['name'] ?></label>
				</th>

				<td style="white-space: nowrap;width:5em;">
				<?php $memo = isset($cs[$code]['memo']) ? $cs[$code]['memo'] : @$bulk[$code]['memo'] ;  ?>
				<textarea name="chk[<?php echo $code ?>][memo]"><?php echo $memo ?></textarea>
				</td>

				<td style="white-space: nowrap;width:5em;">
				<select name="chk[<?php echo $code ?>][uid]">
				<?php
				foreach ($users as $uid => $name):
					$selected = '';
					if (
						isset($cs[$code]['uid']) && $cs[$code]['uid'] = $uid ||
						isset($bulk[$code]['uid']) && $bulk[$code]['uid'] = $uid
					):
						$selected = ' selected="selected"';
					endif;
				?>
					<option value="<?php echo $uid ?>"><?php echo $name ?></option>
				<?php endforeach;  ?>
				</select>
				</td>
				<td style="white-space: nowrap;width:5em;">
				<a<?php echo A11YC_TARGET ?> href="<?php echo A11YC_DOC_URL.$code ?>&amp;criterion=<?php echo $kkk ?>">how to</a>
				</td>
				</tr>
			<?php endforeach;  ?>
			</tbody></table>
			</div><!--/#c_<?php echo $kkk ?>.l_<?php echo strtolower($vvv['level']['name']) ?>-->
		<?php endforeach;  ?>
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach;  ?>
	</div><!--/#section_p_<?php echo $v['code'] ?>.section_guidelines-->
<?php endforeach;  ?>
<input type="hidden" value="<?php echo s($url) ?>" />
</div><!-- /#a11yc_checks -->
