<!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php if ($title) echo $title.' - '; echo A11YC_LANG_POST_SERVICE_NAME ?></title>

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
<!-- #a11yc -->
<div id="<?php echo 'a11yc_'.$mode ?>" class="a11yc">

<!-- mainmenu -->
<p>
<a href="<?php echo $base_url ?>"><?php echo A11YC_LANG_POST_INDEX ?></a>
<a href="<?php echo $base_url ?>?a=readme"><?php echo A11YC_LANG_POST_README ?></a>
<a href="<?php echo $base_url ?>?a=docs"><?php echo A11YC_LANG_DOCS_TITLE ?></a>
<?php if (\A11yc\Auth::auth()): ?>
	<a href="<?php echo $base_url ?>?a=logout"><?php echo A11YC_LANG_LOGOUT ?></a>
<?php else: ?>
	<a href="<?php echo $base_url ?>?a=login"><?php echo A11YC_LANG_AUTH_TITLE ?></a>
<?php endif; ?>
</p>
<!-- /mainmenu -->

<h1><?php echo A11YC_LANG_POST_TITLE; if ( ! $title) echo ' ver.'.A11YC_VERSION; ?></h1>
