<?php namespace A11yc; ?>
<!-- setting -->
<form action="<?php echo Util::uri() ?>" method="POST">
<?php echo $form; ?>
	<div id="a11y_submit">
		<input type="submit" value="<?php echo A11YC_LANG_CTRL_SEND ?>" />
	</div>
</form>

<h2><?php echo A11YC_LANG_DISCLOSURE_PROTECT_VERSION_TITLE ?></h2>
<form action="<?php echo Util::uri() ?>" method="POST">
	<div>
	<p><?php echo A11YC_LANG_DISCLOSURE_PROTECT_VERSION_EXP ?><p>
	<input type="hidden" name="protect_data" value="1" />
	<input type="submit" value="<?php echo A11YC_LANG_DISCLOSURE_PROTECT_VERSION_TITLE ?>" data-a11yc-confirm="<?php echo A11YC_LANG_DISCLOSURE_PROTECT_DATA_CONFIRM ?>" />
	</div>
</form>

if ($versions)
{
	$html.= '<h3>'.A11YC_LANG_DISCLOSURE_VERSION_EXISTS.'</h3>';
	$html.= '<p>'.A11YC_LANG_DISCLOSURE_VERSION_EXISTS_EXP.'</p><ul>';
	foreach ($versions as $version)
	{
		$html.= '<li>'.date('Y-m-d', strtotime($version)).'</li>';
	}
	$html.= '</ul>';
	echo $html;
}
