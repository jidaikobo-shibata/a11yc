<?php namespace A11yc; ?>
<!-- by url -->
<form action="<?php echo $script_url ?>" method="POST" class="a11yc_validator">
<h2>URL</h2>
<label for="url">URL
	<input type="text" name="url" id="url" size="35" value="<?php echo $url ?>">
</label>
<label for="user_agent">User Agent
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
</label>

<section>
	<label for="doc_root"><?php echo A11YC_LANG_BASE_URL ?> (ex: <code>http://example.com</code>)
		<input type="text" name="doc_root" id="doc_root" size="30" value="<?php echo $doc_root ?>">
	</label>
</section>

<label>
	<input type="checkbox" name="do_css_check" value="1"<?php if ($do_css_check) echo ' checked="checked"'; ?> />
	<?php echo A11YC_LANG_CHECKLIST_DO_CSS_CHECK ?>
</label>

<div class="a11yc_submit_group">
	<label for="behaviour"><?php echo A11YC_LANG_POST_BEHAVIOUR ?>
		<select name="behaviour" id="behaviour">
		<option value="check"><?php echo A11YC_LANG_POST_DO_CHECK ?></option>
		<option value="images"><?php echo A11YC_LANG_POST_SHOW_LIST_IMAGES ?></option>
	</select>
	</label>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</div>
</form>

<!-- by html -->
<form action="<?php echo $script_url ?>" method="POST" class="a11yc_validator">
<h2><label for="source">HTML Source</label></h2>
<p><?php echo A11YC_LANG_POST_CANT_SHOW_LIST_IMAGES ?></p>
<textarea name="source" id="source" style="width: 100%; min-height: 10em;"><?php echo $target_html ?></textarea>
<div class="a11yc_submit_group">
	<label for="behaviour2"><?php echo A11YC_LANG_POST_BEHAVIOUR ?>
		<select name="behaviour" id="behaviour2">
		<option value="check"><?php echo A11YC_LANG_POST_DO_CHECK ?></option>
	</select>
	</label>
	<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
</div>
</form>

<?php
if ((Input::isPostExists() || Input::get('url')) && isset($result)):
?>
<div id="a11yc_validator_results">
<h2><?php echo A11YC_LANG_CHECKLIST_CHECK_RESULT ?></h2>
<table id="a11yc_targetpage_info">
<?php if (isset($page_title) && ! empty($page_title)): ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_PAGE_PAGETITLE ?></th>
		<td><?php echo $page_title ?></td>
	</tr>
<?php endif; ?>
<?php /* ?>
	<tr>
		<th scope="row"><?php echo A11YC_LANG_CHECKLIST_REAL_URL ?></th>
		<td><?php echo $real_url ?></td>
	</tr>
<?php */ ?>
	<tr>
		<th scope="row">User Agent</th>
		<td><?php echo $current_user_agent; ?></td>
	</tr>
</table>
<?php
	echo $result;
	echo '</div><!-- /#a11yc_validator_results -->';
endif;
?>
