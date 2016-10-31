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
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo A11YC_URL_DIR ?>/js/a11yc.js"></script>

	<!--css-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo A11YC_URL_DIR ?>/css/a11yc.css" />
	<link href="<?php echo A11YC_URL_DIR ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>

<!-- #a11yc -->
<div id="<?php echo 'a11yc_'.$mode ?>" class="a11yc">
<nav id="a11yc_menu">
<?php if (\Kontiki\Auth::auth()): ?>
<a href="#a11yc_content" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_JUMP_TO_CONTENT ?></a>
<ul>
	<li class="a11yc_menu_item a11yc_center"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=center&amp;a=index"><?php echo A11YC_LANG_CENTER_TITLE ?></a></li>
	<li class="a11yc_menu_item a11yc_setup"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=setup&amp;a=index"><?php echo A11YC_LANG_SETUP_TITLE ?></a></li>
	<li class="a11yc_menu_item a11yc_pages"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=pages&amp;a=index"><?php echo A11YC_LANG_PAGES_TITLE ?></a></li>
	<li class="a11yc_menu_item a11yc_bulk"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=bulk&amp;a=index"><?php echo A11YC_LANG_BULK_TITLE ?></a></li>
	<li class="a11yc_menu_item a11yc_docs"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=docs&amp;a=index"><?php echo A11YC_LANG_DOCS_TITLE ?></a></li>
	<li class="a11yc_menu_item a11yc_logout a11yc_fr"><span class="a11yc_fa_icon" role="presentation" aria-hidden="true"></span><a href="?c=auth&amp;a=logout"><?php echo \A11yc\Users::fetch_current_user()[0].'&nbsp;:&nbsp;'.A11YC_LANG_LOGOUT ?></a></li>
	<li class="a11yc_menu_item a11yc_dev_info a11yc_fr"><a href=""><?php echo \Kontiki\Performance::calc_time().' '.\Kontiki\Performance::calc_memory() ?></a></li>
</ul>
<?php endif; ?>
</nav>
<?php
	$h1 = constant('A11YC_LANG_'.strtoupper($mode).'_TITLE');
	$titile_attr_str ='';
	if($mode == 'docs'):
		if(!(isset($_GET['a']) && $_GET['a']=='each')):
			$titile_attr_str = ' class="a11yc_hasctrl"';
		else:
			$h1 .= ':&nbsp;'.$doc['name'];
		endif;
	endif;
?>
<a href="javascript:void(0);" id="a11yc_content" class="a11yc_skip a11yc_show_if_focus"><?php echo A11YC_LANG_BEGINNING_OF_THE_CONTENT ?></a>
<h1<?php echo $titile_attr_str ?>><?php echo $h1 ?></h1>
