<?php
$errors = \A11yc\Session::fetch('messages', 'errors');
if ($errors):
?>
<ul id="a11yc_msg_error" class="a11yc_msg">
<?php foreach ($errors as $error): ?>
	<li><?php echo $error; ?></li>
<?php endforeach; ?>
</ul>
<?php
endif;

$messages = \A11yc\Session::fetch('messages', 'messages');
if ($messages):
?>
<ul id="a11yc_msg_info" class="a11yc_msg">
<?php foreach ($messages as $message): ?>
	<li><?php echo $message; ?></li>
<?php endforeach; ?>
</ul>
<?php
endif;
?>