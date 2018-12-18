<?php namespace A11yc; ?>

<?php
if (Auth::auth()):
	echo A11YC_NON_DOWNLOAD_START;
	echo '<ul>';
	echo '<li><a href="'.A11YC_EXPORT_URL.'resultexport">'.A11YC_LANG_PAGES_LABEL_EXPORT_CHECK_RESULT.'</a><br /></li>';
	echo '<li><a href="'.A11YC_EXPORT_URL.'resultimport">'.A11YC_LANG_PAGES_LABEL_IMPORT_CHECK_RESULT.'</a></li>';
	echo '<li><a href="'.$download_link.'">'.A11YC_LANG_PAGES_LABEL_EXPORT_RESULT_HTML.'</a></li>';
	echo '</ul>';
	echo A11YC_NON_DOWNLOAD_END;
endif;


echo $result;
echo $implements_checklist;
?>

<?php if ( ! Input::get('url')): ?>
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
<img src="<?php echo A11YC_ASSETS_URL ?>/img/logo.png" id="a11yc_logo" alt="A11yc Logo">
<p><?php echo A11YC_LANG_CENTER_ABOUT_CONTENT ?></p>
</div><!-- /.a11yc_cmt -->
<?php endif; ?>
