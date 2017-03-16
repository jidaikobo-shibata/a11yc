<?php namespace A11yc; ?>
<!-- by url -->
<form action="<?php echo $target_url ?>" method="POST" class="a11yc_validator">
<h2>URL</h2>
<label for="url">URL</label>
<input type="text" name="url" id="url" size="35" value="<?php echo $url ?>">

<label for="user_agent">User Agent</label>
<?php
$uas = array(
	'using' => A11YC_LANG_UA_USING,
	'iphone' => A11YC_LANG_UA_IPHONE,
	'android' => A11YC_LANG_UA_ANDROID,
	'ipad' => A11YC_LANG_UA_IPAD,
	'tablet' => A11YC_LANG_UA_ANDROID_TABLET,
	'featurephone' => A11YC_LANG_UA_FEATUREPHONE,
);
?>
<select name="user_agent" id="user_agent">
	<?php
		foreach ($uas as $type => $ua):
			$selected = $user_agent == $type ? ' selected="selected"' : '';
	?>
	<option value="<?php echo $type ?>"<?php echo $selected ?>><?php echo $ua ?></option>
	<?php endforeach; ?>
</select>

<div class="a11yc_submit_group">
	<label for="behaviour"><?php echo A11YC_LANG_POST_BEHAVIOUR ?></label>
	<select name="behaviour" id="behaviour">
		<option value="check"><?php echo A11YC_LANG_POST_DO_CHECK ?></option>
		<option value="images"><?php echo A11YC_LANG_POST_SHOW_LIST_IMAGES ?></option>
	</select>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</div>
</form>

<!-- by html -->
<form action="<?php echo $target_url ?>" method="POST" class="a11yc_validator">
<h2><label for="source">HTML Source</label></h2>
<p><?php echo A11YC_LANG_POST_CANT_SHOW_LIST_IMAGES ?></p>
<textarea name="source" id="source" style="width: 100%; min-height: 10em;"><?php echo $target_html ?></textarea>
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>

<?php
if (Input::post() && isset($result)):
?>
<div id="a11yc_validator_results">
<h2><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></h2>
<table id="a11yc_targetpage_info">
	<tr>
		<th scope="row"><?php echo A11YC_LANG_PAGES_PAGETITLE ?></th>
		<td><?php echo $page_title ?></td>
	</tr>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CHECKLIST_REAL_URL ?></th>
		<td><?php echo $real_url ?></td>
	</tr>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CHECKLIST_UA ?></th>
		<td><?php echo $current_user_agent; ?></td>
	</tr>
</table>
<?php
	echo $result;
	echo '</div><!-- /#a11yc_validator_results -->';
endif;
?>