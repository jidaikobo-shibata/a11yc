<?php
namespace A11yc;
$errors = Session::fetch('messages', 'errors');

if ($errors || (Auth::auth() && Maintenance::isUgingLower())):
?>
<ul id="a11yc_msg_error" class="a11yc_msg a11yc_hide_if_fixedheader">
<?php
// ordinary errors
if (is_array($errors)):
foreach ($errors as $error):
?>
	<li><?php echo Util::s($error); ?></li>
<?php
endforeach;
endif;

// version message
if (Auth::auth() && Maintenance::isUgingLower()):
?>
	<li><?php echo sprintf(
		A11YC_LANG_ERROR_GET_NEW_A11YC,
		'https://github.com/jidaikobo-shibata/a11yc',
		A11YC_VERSION,
		Util::s(Maintenance::getLatestVersion())
	) ?></li>
<?php endif; ?>
</ul>
<?php
endif;

$messages = Session::fetch('messages', 'messages');
if (is_array($messages)):
?>
<ul id="a11yc_msg_info" class="a11yc_msg a11yc_hide_if_fixedheader">
<?php foreach ($messages as $message): ?>
	<li><?php echo Util::s($message); ?></li>
<?php endforeach; ?>
</ul>
<?php
endif;
