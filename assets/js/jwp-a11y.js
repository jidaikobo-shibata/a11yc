jQuery(function($){
	$.ajax({
		type: 'POST',
		url: jwp_a11y_ajax.ajax_url,
		dataType: 'json',
		data: {
			action: 'jwp_a11y_ajax_validate',
			url: jwp_a11y_ajax.target_url,
			link_check: jwp_a11y_ajax.link_check
		},
		beforeSend: function() {
			$('#a11yc_validator_results').addClass('a11yc_loading');
		},
		success: function(data) {
			$('#a11yc_validator_results').removeClass('a11yc_loading').append(data.data);
			$.fn.a11yc_disclosure();
			if(!$('.a11yc_fixed_header')[0]) $.fn.a11yc_get_height();;
			$.fn.a11yc_format_validation_error();
//			$.fn.a11yc_set_validation_code_txt();
		},
		error:function() {
			$('#a11yc_validator_results').removeClass('a11yc_loading').text('failed');
		}
	});
})
