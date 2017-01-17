<?php
if ( ! headers_sent()):
	header("HTTP/1.1 200 OK");
	header('Content-Type: text/html; charset=utf-8');
?>
	<!DOCTYPE html><html lang="<?php echo A11YC_LANG ?>"><head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11YC</title></head><body>
<?php endif; ?>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo A11YC_URL_DIR ?>/js/a11yc.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_URL_DIR ?>/css/a11yc.css" />

<div style="word-break: break-all;">
<script>a11yc_auto_scroll()</script>
<h1><?php echo $title ?></h1>
<p><?php echo A11YC_LANG_PAGES_IT_TAKES_TIME ?></p>
<hr />
