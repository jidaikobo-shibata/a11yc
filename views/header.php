<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11yC</title>

	<!--favicon-->
	<link href="<?php echo A11YC_ASSETS_URL ?>/img/favicon.ico" rel="SHORTCUT ICON" />

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--script-->
	<script src="<?php echo A11YC_ASSETS_URL ?>/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo A11YC_ASSETS_URL ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_ASSETS_URL ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_ASSETS_URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body<?php echo Model\Version::current() != 0 ? ' class="a11yc_use_old_version"' : ''; ?>>

<!-- #a11yc -->
<div id="<?php echo 'a11yc_'.$mode ?>" class="a11yc">
<?php if (Auth::auth() && isset($login_user[2])): ?>
	<div id="a11yc_menu_wrapper">

		<nav id="a11yc_menu">
		<h1 id="a11yc_title"><img src="<?php echo A11YC_ASSETS_URL ?>/img/logo_w.png" alt="A11yC" /></h1>
		<a href="#a11yc_content" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_JUMP_TO_CONTENT ?></a>
		<ul>
			<li class="a11yc_menu_item a11yc_center"><a href="?c=center&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_CENTER_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_setting"><a href="?c=setting&amp;a=base" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_SETTING_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_icl"><a href="?c=icl&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_ICL_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_page"><a href="?c=page&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_PAGE_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_sitecheck"><a href="?c=sitecheck&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_SITECHECK_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_issue"><a href="?c=issue&amp;a=yet" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_ISSUE_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_bulk"><a href="?c=bulk&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_BULK_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_doc"><a href="?c=doc&amp;a=index" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_DOC_TITLE ?></a></li>
			<li class="a11yc_menu_item a11yc_logout a11yc_fr" title="<?php echo Performance::calcTime().' '.Performance::calcMemory() ?>"><?php echo $login_user[2].'&nbsp;' ?> <a href="?c=auth&amp;a=logout" class="a11yc_hasicon"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><?php echo A11YC_LANG_LOGOUT ?></a></li>
		</ul>
		</nav><!--#a11yc_menu-->

	</div><!--#a11yc_menu_wrapper-->
	<a id="a11yc_content" tabindex="0" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_BEGINNING_OF_THE_CONTENT ?></a>
<?php endif; ?>
<?php
	$h1 = $title;
	$titile_attr_str ='';
	if($mode == 'docs'):
		if( ! (Input::get('a') == 'each')):
			$titile_attr_str = ' class="a11yc_hasctrl"';
		else:
			$h1 .= ':&nbsp;'.$doc['name'];
		endif;
	endif;
?>
<h1<?php echo $titile_attr_str ?>><?php echo $h1 ?></h1>
