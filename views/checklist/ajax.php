<?php
// load JavaScript
wp_enqueue_script(
	'jwp_a11y_ajax',
	plugin_dir_url(dirname(dirname(__FILE__))).'/assets/js/jwp-a11y.js',
	array('jquery'),
	false);

wp_localize_script('jwp_a11y_ajax', 'jwp_a11y_ajax', array(
	'ajax_url' => admin_url('admin-ajax.php'),
	'target_url' => $url,
	'link_check' => $link_check,
));
?>

<!-- #jwp-a11yc_validator_results -->
<div id="a11yc_validator_results"></div>