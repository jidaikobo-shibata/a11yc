<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php if ($title) echo $title.' - '; echo A11YC_LANG_POST_SERVICE_NAME ?></title>

	<!--favicon-->
	<link href="<?php echo A11YC_ASSETS_URL ?>/img/favicon.ico" rel="SHORTCUT ICON" />

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_ASSETS_URL ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

	<!--Search engine-->
	<meta name="description" content="ウェブアクセシビリティのチェッカーです。だれでもお使いいただけます。" />

	<!--OGP-->
	<meta property="og:locale" content="ja_JP" />
	<meta property="og:title" content="<?php echo $title ?: A11YC_LANG_POST_SERVICE_NAME ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo s(Util::uri()) ?>" />
	<meta property="og:site_name" content="<?php echo A11YC_LANG_POST_SERVICE_NAME ?>" />
	<meta property="og:image" content="https://a11yc.com/logo.png" />
	
	<!-- Twitter card -->
	<meta name="twitter:card" content="summary" />

<?php
if (A11YC_POST_GOOGLE_ANALYTICS_CODE)
{
	echo "	<!--Google analytics-->";
	echo A11YC_POST_GOOGLE_ANALYTICS_CODE;
}
?>
</head>
<body>
<!-- #a11yc -->
<div id="<?php echo 'a11yc_'.$mode ?>" class="a11yc">
	<div id="a11yc_menu_wrapper">
	<nav id="a11yc_menu">
	<h1 id="a11yc_title"><img src="<?php echo A11YC_ASSETS_URL ?>/img/logo_w.png" alt="A11yC logo" /></h1>
	<ul>
		<li class="a11yc_menu_item a11yc_validation"><a href="<?php echo $base_url ?>" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_POST_INDEX ?></a></li>
		<li class="a11yc_menu_item a11yc_readme"><a href="<?php echo $base_url ?>?a=readme" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_POST_README ?></a></li>
		<li class="a11yc_menu_item a11yc_docs"><a href="<?php echo $base_url ?>?a=docs" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_DOCS_TITLE ?></a></li>
<?php if (Auth::auth()): ?>
		<li class="a11yc_menu_item a11yc_logout a11yc_fr"><a href="<?php echo $base_url ?>?a=logout" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_LOGOUT ?></a></li>
<?php else: ?>
		<li class="a11yc_menu_item a11yc_login a11yc_fr"><a href="<?php echo $base_url ?>?a=login" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_AUTH_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_dev_info a11yc_fr"><span role="presentation"><?php echo \Kontiki\Performance::calc_time().' '.\Kontiki\Performance::calc_memory() ?></span></li>
<?php endif; ?>
		</ul>
	</nav><!--/#a11yc_menu-->
</div><!--#a11yc_menu_wrapper-->

<h1><?php echo A11YC_LANG_POST_TITLE; if ( ! $title) echo ' ver.'.A11YC_VERSION; ?></h1>
