jQuery(function($){
	// overlay for disable buttons
	$('body').append('<div id="a11yc_overlay" />');

	// error text
	$('.a11yc_validation_code_error').each(function(){
		$('<span class="a11yc_error_text" />').text($(this).attr('title')).appendTo(this);
	});
	
	// alt
	$('img').each(function(){
		$(this).wrap('<span class="a11yc_live_img_wrapper">');
		$('<span class="a11yc_live_alt" />').text('alt="'+$(this).attr('alt')+'"').insertBefore(this);
	});
});


