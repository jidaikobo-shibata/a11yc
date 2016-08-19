<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11YC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_URL_DIR ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_URL_DIR ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>

<!-- #a11yc -->
<div id="<?php echo 'a11yc_'.$mode ?>" class="a11yc">

<?php if ($mode != 'login'): ?>
<nav>
<ul>
	<li><a href="?mode=center"><?php echo A11YC_LANG_CENTER_TITLE ?></a></li>
	<li><a href="?mode=setup"><?php echo A11YC_LANG_SETUP_TITLE ?></a></li>
	<li><a href="?mode=pages"><?php echo A11YC_LANG_PAGES_TITLE ?></a></li>
	<li><a href="?mode=bulk"><?php echo A11YC_LANG_BULK_TITLE ?></a></li>
	<li><a href="?mode=docs"><?php echo A11YC_LANG_DOCS_TITLE ?></a></li>
	<li><a href="?mode=logout"><?php echo A11YC_LANG_LOGOUT ?></a></li>
</ul>
</nav>
<?php endif; ?>

<h1><?php echo $title ?></h1>
