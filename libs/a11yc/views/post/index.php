<!-- by url -->
<form action="<?php echo \A11yc\Util::uri() ?>" method="POST">
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
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>

<!-- by html -->
<form action="<?php echo \A11yc\Util::uri() ?>" method="POST">
<h2><label for="source">HTML Source</label></h2>
<textarea name="source" id="source" style="width: 100%; min-height: 10em;"><?php echo $target_html ?></textarea>
<input type="hidden" name="url" value="<?php echo $url ?>">
<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</form>

<?php
if (\A11yc\Input::post() && isset($result)):
?>
<h2><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></h2>
<dl id="a11yc_validator_results_info">
	<dt><?php echo A11YC_LANG_PAGES_PAGETITLE ?></dt>
	<dd><?php echo $page_title ?></dd>
	<dt><?php echo A11YC_LANG_CHECKLIST_REAL_URL ?></dt>
	<dd><?php echo $real_url ?></dd>
	<dt><?php echo A11YC_LANG_CHECKLIST_UA ?></dt>
	<dd><?php echo $current_user_agent; ?></dd>
</dl>
<?php
	echo $result;
endif;
?>