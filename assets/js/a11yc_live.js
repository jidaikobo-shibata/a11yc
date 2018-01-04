jQuery(function($){
	// overlay for disable buttons
	$('body').append('<div id="a11yc_overlay">');

	// alt
	$('img').each(function(){
		$(this).wrap('<span class="a11yc_live_img_wrapper">');
		$('<span class="a11yc_live_alt">').text('alt="'+$(this).attr('alt')+'"').insertBefore(this);
	});
	
	// movable labels ?
	$labels = $('.a11yc_validation_code_error');

});
