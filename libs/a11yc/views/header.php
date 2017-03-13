<!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11YC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

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
<?php if (\Kontiki\Auth::auth() && isset($login_user[2])): ?>
	<div id="a11yc_menu_wrapper">
		<nav id="a11yc_menu">
		<h1 id="a11yc_title"><img src="<?php echo A11YC_ASSETS_URL ?>/img/logo_w.png" alt="A11yC logo" /></h1>
		<a href="#a11yc_content" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_JUMP_TO_CONTENT ?></a>
		<ul>
			<li class="a11yc_menu_item a11yc_center"><a href="?c=center&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_CENTER_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_setup"><a href="?c=setup&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_SETUP_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_pages"><a href="?c=pages&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_PAGES_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_bulk"><a href="?c=bulk&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_BULK_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_docs"><a href="?c=docs&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_DOCS_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_logout a11yc_fr"><?php echo $login_user[2].'&nbsp;' ?> <a href="?c=auth&amp;a=logout" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_LOGOUT ?></a></li>
		<?php if ($login_user[0] == 'root'): ?>
			<li class="a11yc_menu_item a11yc_dev_info a11yc_fr"><span role="presentation"><?php echo \Kontiki\Performance::calc_time().' '.\Kontiki\Performance::calc_memory() ?></span></li>
		<?php endif; ?>
		</ul>
		</nav><!--#a11yc_menu-->
	</div><!--#a11yc_menu_wrapper-->
	<a id="a11yc_content" tabindex="0" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_BEGINNING_OF_THE_CONTENT ?></a>
<?php endif; ?>
<?php
	$h1 = constant('A11YC_LANG_'.strtoupper($mode).'_TITLE');
	$titile_attr_str ='';
	if($mode == 'docs'):
		if( ! (\A11yc\Input::get('a') == 'each')):
			$titile_attr_str = ' class="a11yc_hasctrl"';
		else:
			$h1 .= ':&nbsp;'.$doc['name'];
		endif;
	endif;
?>
<h1<?php echo $titile_attr_str ?>><?php echo $h1 ?></h1>
