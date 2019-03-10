<?php namespace A11yc; ?>

<form action="<?php echo Util::uri() ?>" method="GET">
	<input type="hidden" name="c" value="bulk">
	<input type="hidden" name="a" value="criterion">
	<ul class="a11yc_submenu">
		<li><a href="<?php echo A11YC_BULK_URL ?>index"><?php echo A11YC_LANG_BULK_TITLE ?></a></li>
		<li style="white-space: nowrap;">
			<label><?php echo A11YC_LANG_BULK_CRITERION_LABEL ?>
			<select name="focus">
				<?php $selected = Input::get('focus', '') == 'result' ? ' selected="selected"' : '' ; ?>
				<option<?php echo $selected ?> value="result"><?php echo A11YC_LANG_BULK_CRITERION_TITLE ?></option>
				<?php $selected = Input::get('focus', '') == 'icl' ? ' selected="selected"' : '' ; ?>
				<option<?php echo $selected ?> value="icl"><?php echo A11YC_LANG_BULK_ICL_TITLE ?></option>
				<?php $selected = Input::get('focus', '') == 'check' ? ' selected="selected"' : '' ; ?>
				<option<?php echo $selected ?> value="check"><?php echo A11YC_LANG_BULK_ICL_TECH_TITLE ?></option>
				<?php $selected = Input::get('focus', '') == 'failure' ? ' selected="selected"' : '' ; ?>
				<option<?php echo $selected ?> value="failure"><?php echo A11YC_LANG_BULK_ICL_FAILURE_TITLE ?></option>
			</select></label>

			<label><?php echo A11YC_LANG_CRITERION ?>
			<select name="criterion" style="width: 15em">
			<?php
			// don't use variable name "$criterion". it cause bad infection other templates.
			foreach (Yaml::each('criterions') as $criterion_code => $v):
				$selected = Input::get('criterion', '') == $criterion_code ? ' selected="selected"' : '' ;
				echo '<option'.$selected.' value="'.$criterion_code.'">'.Util::key2code($criterion_code).' '.$v['name'].'</option>';
			endforeach;
			?>
			</select></label>

			<?php $checked = Input::get('integrate', false) ? ' checked="checked"' : '' ; ?>
			<label><input<?php echo $checked ?> type="checkbox" name="integrate" value="1"><?php echo A11YC_LANG_INTEGRATE ?></label>
			<input style="display:inline" type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>">
		</li>
	</ul>
</form>
