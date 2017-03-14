<?php
namespace A11yc;
$errors = Session::fetch('messages', 'errors');
if ($errors || Maintenance::is_uging_lower()):
?>
<ul id="a11yc_msg_error" class="a11yc_msg">
<?php
// ordinary errors
if ($errors):
foreach ($errors as $error):
?>
	<li><?php echo Util::s($error); ?></li>
<?php
endforeach;
endif;

// version message
if (Maintenance::is_uging_lower()):
?>
	<li><?php echo sprintf(
		A11YC_LANG_ERROR_GET_NEW_A11YC,
		'https://github.com/jidaikobo-shibata/a11yc',
		A11YC_VERSION,
		Util::s(Maintenance::get_stored_version())
	) ?></li>
<?php endif; ?>
</ul>
<?php
endif;

$messages = Session::fetch('messages', 'messages');
if ($messages):
?>
<ul id="a11yc_msg_info" class="a11yc_msg">
<?php foreach ($messages as $message): ?>
	<li><?php echo Util::s($message); ?></li>
<?php endforeach; ?>
</ul>
<?php
endif;
