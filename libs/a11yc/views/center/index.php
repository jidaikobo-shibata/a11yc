<!-- Bookmarklet -->
<h2>Bookmarklet</h2>

<?php echo A11YC_LANG_CENTER_BOOKMARKLET_EXP; ?>

<p><a href='javascript:(function(){var%20a11yc_pass,url;a11yc_pass="<?php echo A11YC_CHECKLIST_URL; ?>";url=encodeURI(location.href);window.document.location=a11yc_pass+url;})();'>A11yc checker</a></p>

<textarea style="width:100%;height:8.25em;">
javascript:(function(){
	var a11yc_pass,url;
	a11yc_pass="<?php echo A11YC_CHECKLIST_URL; ?>";
	url=encodeURI(location.href);
	window.document.location=a11yc_pass+url;
})();
</textarea>
<!-- /Bookmarklet -->

<div id="a11yc_center_about" class="a11yc_cmt">
<h2><?php echo A11YC_LANG_CENTER_ABOUT ?></h2>
<img src="<?php echo A11YC_ASSETS_URL ?>/img/logo_author.png" id="a11yc_logo_author" alt="<?php echo A11YC_LANG_CENTER_LOGO ?>">
<p><?php echo A11YC_LANG_CENTER_ABOUT_CONTENT ?></p>
<div><!-- /.a11yc_cmt -->
