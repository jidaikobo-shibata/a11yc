<div id="a11yc_checks" data-a11yc-current-user="<?php echo $current_user_id ?>">

<p id="a11yc_narrow_level" class="a11yc_hide_if_no_js">Level:

<?php
$level_strs = array(
	'A'   => 'l_a',
	'AA'  => 'l_a,l_aa',
	'AAA' => 'l_a,l_aa,l_aaa'
);
$i = 0;
foreach ($level_strs as $level_label => $level_str):
	$class_str = $target_level == ++$i ? ' class="current"' : '';
?>
	<a role="button" tabindex="0" data-narrow-level="<?php echo $level_str ?>"<?php echo $class_str ?>><?php echo $level_label ?></a>
<?php
endforeach;
?>
</p>

<!-- header -->
<div id="a11yc_header">

<p id="a11yc_info"><?php echo A11YC_LANG_CHECKLIST_RESTOFNUM ?>:<span></span></p>
<p><a href="<?php echo s(urldecode($url)) ?>">back to target page</a></p>

<!-- level -->
<p><?php echo A11YC_LANG_TARGET_LEVEL ?>: <?php echo \A11yc\Util::num2str($target_level) ?></p>
<?php $current_level = $target_level ? \A11yc\Evaluate::result_str($page['level'], $target_level) : '-';  ?>
<p><?php echo A11YC_LANG_CURRENT_LEVEL ?>: <?php echo $current_level ?></p>

<!-- not for bulk -->
<?php if ($url != 'bulk'):  ?>
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
		<ul style="height: 200px; overflow: auto; border: 1px #aaa solid;">
		<?php foreach ($errs as $err):  ?>
			<li><?php echo $err ?></li>
		<?php endforeach;  ?>
		</ul>
	<?php else:
		echo A11YC_LANG_CHECKLIST_NOT_FOUND_ERR;
	endif;  ?>
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
	<div id="section_p_<?php echo $v['code'] ?>" class="section_guidelines"><h2 id="p_<?php echo $v['code'] ?>" tabindex="-1"><?php echo $v['code'].' '.$v['name'] ?></h2>

	<!-- guidelines -->
	<?php
	foreach ($yml['guidelines'] as $kk => $vv):
		if ($kk{0} != $k) continue;
	?>
		<div id="g_<?php echo $vv['code'] ?>" class="section_guideline"><h3><?php echo \A11yc\Util::key2code($vv['code']).' '.$vv['name'] ?></h3>

		<!-- criterions -->
		<div class="section_criterions">
		<?php
		foreach ($yml['criterions'] as $kkk => $vvv):
			if (substr($kkk, 0, 3) != $kk) continue;
		?>
			<div id="c_<?php echo $kkk ?>" class="section_criterion l_'.strtolower($vvv['level']['name']).'">
			<div class="a11yc_criterion">
			<h4><?php echo \A11yc\Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')' ?>
			<?php if (isset($vvv['url_as'])):  ?>
				<a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url_as'] ?>" class="link_as">Accessibility Supported</a>
			<?php endif;  ?>

			<a<?php echo A11YC_TARGET ?> href="<?php echo $vvv['url'] ?>" class="link_understanding">Understanding</a></h4>

			<p><?php echo $vvv['summary'] ?></p></div><!-- /.a11yc_criterion -->

			<!-- checks -->
			<table><tbody>
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
		</div><!--/.section_criterions-->
		</div><!--/#g_<?php echo $vv['code'] ?>-->
	<?php endforeach;  ?>
	</div><!--/#section_p_<?php echo $v['code'] ?>.section_guidelines-->
<?php endforeach;  ?>
<input type="hidden" value="<?php echo s($url) ?>" />
</div><!-- /#a11yc_checks -->
