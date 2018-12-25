<?php namespace A11yc; ?>
<h2><?php echo A11YC_LANG_SETTINGS_TITLE_BASE ?></h2>

<table class="a11yc_table a11yc_setting">
	<tbody>

	<tr>
		<th scope="row"><label for="a11yc_show_policy"><?php echo A11YC_LANG_CTRL_VIEW ?></label></th>
		<td>
			<?php $checked = Arr::get($settings, 'show_results') == 1 ? ' checked="checked"' : ''; ?>
			<label><input type="checkbox" name="show_results" value="1"<?php echo $checked ?> /> <?php echo A11YC_LANG_SHOW_RESULTS ?></label>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_client_name"><?php echo A11YC_LANG_CLIENT_NAME ?></label></th>
		<td>
			<input type="text" name="client_name" id="a11yc_client_name" size="40" style="width: 100%;" value="<?php echo @$settings['client_name'] ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_declare_date"><?php echo A11YC_LANG_DECLARE_DATE ?></label></th>
		<td>
			<input type="text" name="declare_date" id="a11yc_declare_date" size="20" value="<?php echo @$settings['declare_date'] ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_standard"><?php echo A11YC_LANG_STANDARD ?></label></th>
		<td>
			<select name="standard" id="a11yc_standard">
				<?php
					foreach ($standards as $k => $v):
					$selected = $k == Arr::get($settings, 'standard') ? ' selected="selected"' : '';
				?>
				<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_target_level"><?php echo A11YC_LANG_TARGET_LEVEL ?></label></th>
		<td>
			<select name="target_level" id="a11yc_target_level">
				<?php
					foreach (array('-', 'A', 'AA', 'AAA') as $k => $v):
					$selected = Arr::get($settings, 'target_level') == $k ? ' selected="selected"' : '';
				?>
				<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_selected_method"><?php echo A11YC_LANG_CANDIDATES_TITLE ?></label></th>
		<td>
			<select name="selected_method" id="a11yc_selected_method">
				<?php
					foreach (Values::selectedMethods() as $k => $v):
					$selected = @$settings['selected_method'] == $k ? ' selected="selected"' : '';
				?>
				<option<?php echo $selected ?> value="<?php echo $k ?>"><?php echo $v ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_test_period"><?php echo A11YC_LANG_TEST_PERIOD ?></label></th>
		<td>
			<input type="text" name="test_period" id="a11yc_test_period" size="30" value="<?php echo Util::s(@$settings['test_period']) ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_dependencies"><?php echo A11YC_LANG_DEPENDENCIES ?></label></th>
		<td>
			<textarea name="dependencies" id="a11yc_dependencies" style="width:100%;" rows="7"><?php echo Util::s(@$settings['dependencies']) ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_policy"><?php echo A11YC_LANG_POLICY ?></label></th>
		<td>
			<p><?php echo A11YC_LANG_POLICY_DESC ?></p>
			<div class="a11yc_policy_sample"><?php echo nl2br($sample_policy) ?></div>
			<textarea name="policy" id="a11yc_policy" style="width:100%;" rows="7"><?php echo Util::s(@$settings['policy']) ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_report"><?php echo A11YC_LANG_OPINION ?></label></th>
		<td>
			<p><?php echo A11YC_LANG_REPORT_DESC ?></p>
			<textarea name="report" id="a11yc_report" style="width:100%;" rows="7"><?php echo Util::s(@$settings['report']) ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="a11yc_contact"><?php echo A11YC_LANG_CONTACT ?></label></th>
		<td>
			<p><?php echo A11YC_LANG_CONTACT_DESC ?></p>
			<textarea name="contact" id="a11yc_contact" style="width:100%;" rows="7"><?php echo Util::s(@$settings['contact']) ?></textarea>
		</td>
	</tr>

<?php
$levels = array(
	'A' => 1,
	'AA' => 2,
	'AAA' => 3,
);
?>

<?php if (@$settings['target_level'] && $settings['target_level'] != 3): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL ?></th>
		<td>
			<ul class="a11yc_serialized_values">
			<?php
				$n = 0;
				foreach ($yml['criterions'] as $code => $v):
					if ($levels[$v['level']['name']] <= $settings['target_level']) continue;
					echo ($n % 2 === 0) ? '<li class="odd">' : '<li class="even">';
					$checked = in_array($code, Model\Settings::fetch('additional_criterions')) ? ' checked="checked"' : '';
					echo '<label for="additional_criterions_'.$code.'"><input'.$checked.' type="checkbox" name="additional_criterions['.$code.']" id="additional_criterions_'.$code.'" value="1" /> '.$v['code'].' '.$v['name'].' ('.$v['level']['name'].')</label></li>'."\n";
					$n++;
				endforeach;
			?>
			</ul>
		</td>
	</tr>
<?php endif; ?>

<?php if (@$settings['target_level']): ?>
	<tr>
		<th><?php echo A11YC_LANG_SETTINGS_EXIST_NON_AND_PASS ?></th>
		<td>
			<ul class="a11yc_serialized_values">
			<?php
				$n = 0;
				foreach ($yml['criterions'] as $code => $v):
					if ($levels[$v['level']['name']] > $settings['target_level']) continue;
					echo ($n % 2 === 0) ? '<li class="odd">' : '<li class="even">';
					$checked = in_array($code, Model\Settings::fetch('non_exist_and_passed_criterions')) ? ' checked="checked"' : '';
					echo '<label for="non_exist_and_passed_criterions_'.$code.'"><input'.$checked.' type="checkbox" name="non_exist_and_passed_criterions['.$code.']" id="non_exist_and_passed_criterions_'.$code.'" value="1" /> '.$v['code'].' '.$v['name'].' ('.$v['level']['name'].')</label></li>'."\n";
					$n++;
				endforeach;
			?>
			</ul>
		</td>
	</tr>
<?php endif; ?>

<?php if (@$settings['target_level']): ?>
	<tr>
		<th><?php echo A11YC_LANG_SETTINGS_NON_USE_TECHS ?></th>
		<td>
			<?php
				// JavaScript
				$non_use_techs_candidates = array();
				foreach (Values::nonUseTechsCandidates() as $v):
					$non_use_techs_candidates[] = 'non_use_techs_'.$v;
				endforeach;
				$non_use_techs_str = json_encode($non_use_techs_candidates);
			?>
			<script>
				function a11yc_uncheck_all ()
				{
					$('#non_use_techs_list li input').each(function(){
						$(this).prop("checked", false);
					});
				}

				function a11yc_check_candidates ()
				{
					var non_use_techs = <?php echo $non_use_techs_str ?>;
					Object.keys(non_use_techs).forEach(function (key) {
						$('#non_use_techs_list li input#'+non_use_techs[key]).prop("checked", true);
					});
				}
			</script>
			<?php
				echo '<a href="javascript::void(0)" onclick="a11yc_uncheck_all()">'.A11YC_LANG_PAGES_LABEL_BULK_UNCHECK_ALL.'</a> - ';
				echo '<a href="javascript::void(0)" onclick="a11yc_check_candidates()">'.A11YC_LANG_SETTINGS_NON_USE_TECHS_CANDIDATES.'</a>';
			?>

			<ul id="non_use_techs_list">
			<?php
				foreach ($yml['techs'] as $code => $v):
					$checked = in_array($code, Model\Settings::fetch('non_use_techs')) ? ' checked="checked"' : '';
					echo '<li><label for="non_use_techs_'.$code.'"><input'.$checked.' type="checkbox" name="non_use_techs['.$code.']" id="non_use_techs_'.$code.'" value="1" /> '.$v['title'].'</label></li>'."\n";
					$n++;
				endforeach;
			?>
			</ul>
		</td>
	</tr>
<?php endif; ?>

	</tbody>
</table>

<h2><?php echo A11YC_LANG_SETTINGS_TITLE_ETC ?></h2>

<table class="a11yc_table a11yc_setting">
	<tbody>

	<tr>
		<th><label for="a11yc_base_url"><?php echo A11YC_LANG_SETTINGS_BASE_URL ?></label></th>
		<td><p>ex) http://example.com</p><input type="text" name="base_url" id="a11yc_base_url" style="width: 100%;" value="<?php echo Util::s(@$settings['base_url']) ?>" /></td>
	</tr>

	<tr>
		<th scope="row"><?php echo A11YC_LANG_SETTINGS_BASIC_AUTH_TITLE ?></th>
		<td>
			<p><?php echo A11YC_LANG_SETTINGS_BASIC_AUTH_EXP ?></p>
			<p>
			<label for="a11yc_basic_user"><?php echo A11YC_LANG_SETTINGS_BASIC_AUTH_USER ?></label>
			<input type="text" name="basic_user" id="a11yc_basic_user" size="10" value="<?php echo @$settings['basic_user'] ?>">
			</p>

			<p>
			<label for="a11yc_basic_pass"><?php echo A11YC_LANG_SETTINGS_BASIC_AUTH_PASS ?></label>
			<input type="text" name="basic_pass" id="a11yc_basic_pass" size="10" value="<?php echo @$settings['basic_pass'] ?>">
			</p>
		</td>
	</tr>

	<tr>
		<th><?php echo A11YC_LANG_SETTINGS_IS_USE_GUZZLE ?></th>
		<?php
			$checked = isset($settings['stop_guzzle']) && $settings['stop_guzzle'] ? ' checked="checked"' : '';
		?>
		<td><p><?php echo A11YC_LANG_SETTINGS_IS_USE_GUZZLE_EXP ?></p><label for="a11yc_stop_guzzle"><input type="checkbox" name="stop_guzzle" id="a11yc_stop_guzzle" value="1"<?php echo $checked ?> /><?php echo A11YC_LANG_SETTINGS_IS_USE_GUZZLE ?></label><br /></td>
	</tr>

	<tr>
		<th><label for="a11yc_cache_time"><?php echo A11YC_LANG_SETTINGS_CACHE_TIME ?></label></th>
		<td><p><?php echo A11YC_LANG_SETTINGS_CACHE_TIME_EXP ?></p><input type="text" name="cache_time" id="a11yc_cache_time" size="5" value="<?php echo intval(Arr::get($settings, 'cache_time', '60')) ?>" /></td>
	</tr>

	</tbody>
</table>
