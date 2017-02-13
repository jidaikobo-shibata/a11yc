<?php
$errors = \A11yc\Session::fetch('messages', 'errors');
if ($errors || \A11yc\Maintenance::is_uging_lower()):
?>
<ul id="a11yc_msg_error" class="a11yc_msg">
<?php
// ordinary errors
if ($errors):
foreach ($errors as $error):
?>
	<li><?php echo \A11yc\Util::s($error); ?></li>
<?php
endforeach;
endif;

// version message
if (\A11yc\Maintenance::is_uging_lower()):
?>
	<li><?php echo sprintf(
		A11YC_LANG_ERROR_GET_NEW_A11YC,
		'https://github.com/jidaikobo-shibata/a11yc',
		A11YC_VERSION,
		\A11yc\Util::s(\A11yc\Maintenance::get_stored_version())
	) ?></li>
<?php endif; ?>
</ul>
<?php
endif;

$messages = \A11yc\Session::fetch('messages', 'messages');
if ($messages):
?>
<ul id="a11yc_msg_info" class="a11yc_msg">
<?php foreach ($messages as $message): ?>
	<li><?php echo \A11yc\Util::s($message); ?></li>
<?php endforeach; ?>
</ul>
<?php
endif;
