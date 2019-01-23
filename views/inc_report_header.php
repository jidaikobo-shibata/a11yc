<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11yC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="/assets/css/a11yc.css" />
	<link href="/assets/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
<?php
echo '<div class="a11yc_header">'.Model\Setting::fetch('client_name').'</div>';

echo '<h1>'.$title.'</h1>';
?>
