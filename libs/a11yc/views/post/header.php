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
	<meta name="description" content="<?php echo A11YC_LANG_POST_DESCRIPTION ?>" />

	<!--OGP-->
	<meta property="og:locale" content="<?php echo A11YC_LANG == 'ja' ? 'ja_JP' : 'en_US'  ?>" />
	<meta property="og:title" content="<?php echo $title ?: A11YC_LANG_POST_SERVICE_NAME ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo Util::s(Util::uri()) ?>" />
	<meta property="og:site_name" content="<?php echo A11YC_LANG_POST_SERVICE_NAME ?>" />
	<meta property="og:description" content="<?php echo A11YC_LANG_POST_DESCRIPTION ?>" />
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
			<?php if (in_array(Input::server('REMOTE_ADDR'), array('180.235.36.239', '::1'))): ?>
			<li class="a11yc_menu_item a11yc_dev_info a11yc_fr"><span role="presentation"><?php echo Performance::calcTime().' '.Performance::calcMemory() ?></span></li>
			<?php endif; ?>
<?php endif; ?>
		<li id="social_buttons" class="a11yc_fr">
			<!--Twitter-->
			<a href="https://twitter.com/share" class="twitter-share-button"><?php echo A11YC_LANG_POST_SOCIAL_TWEET ?></a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id))
			{js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

			<!--facebook-->
			<iframe title="<?php echo A11YC_LANG_POST_SOCIAL_FACEBOOK ?>" src="//www.facebook.com/plugins/like.php?href=<?php echo urlencode(Util::s(Util::uri())) ?>&amp;width=72&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21&amp;" style="border:none; overflow:hidden; width:72px; height:21px;" id="facebook_like_button"></iframe>
			<!--?-->
			<a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="basic" title="<?php echo A11YC_LANG_POST_SOCIAL_HATENA ?>"><img src="https://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="<?php echo A11YC_LANG_POST_SOCIAL_HATENA ?>" width="20" height="20" style="border: none;" /></a><script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
		</li>
		</ul>
	</nav><!--/#a11yc_menu-->
</div><!--#a11yc_menu_wrapper-->

<h1><?php echo A11YC_LANG_POST_TITLE; if ( ! $title) echo ' ver.'.A11YC_VERSION; ?></h1>
