jQuery(function($){
	var $labels = $('.a11yc_validation_code_error'),
	    $wrappers = $('.a11yc_live_error_wrapper');

	// overlay for disable buttons
	$('body').append('<div id="a11yc_overlay" />');

	// error text
	$labels.each(function(){
		$('<span class="a11yc_error_text" />').text($(this).attr('title')).appendTo(this);
	});
	
	// alt
	$('img').each(function(){
		$(this).wrap('<span class="a11yc_live_img_wrapper">');
		$('<span class="a11yc_live_alt" />').text('alt="'+$(this).attr('alt')+'"').insertBefore(this);
	});
	
	//tabindex
	$(document).find('a, :input, [tabindex]').attr('tabindex', -1);
	$labels.attr('tabindex', 0);

//	$(window).on('load', function(){
//		console.log($labels);
//	});

	// replace url (CSS background-image and inserted images)
	// need target root pass
	var arg  = {};
	var param = location.search.substring(1).split('&');
	for(i=0; param[i]; i++) {
		var k = param[i].split('=');
		arg[k[0]] = k[1];
	}
	var $obj,
	location_origin = window.location.origin,
	location_target = decodeURIComponent(arg.target);
	setTimeout(function(){
		$(document).find('*').each(function(){
			$obj = $(this);
			if( $obj.css('backgroundImage') != 'none')
			{
				var url = /^url\((['"]?)(.*)\1\)$/.exec($(this).css('backgroundImage'));
				url = url[2];
				if (url.indexOf(location_origin) == 0)
				{
					url = url.replace(location_origin,  location_target);
					$obj.css('backgroundImage', 'url("'+url+'")');
				}
			}
			else if( $obj[0].src)
			{
				var url = $obj[0].src;
				if (url.indexOf(location_origin) == 0)
				{
					url = url.replace(location_origin,  location_target);
					$obj.attr('src', url);
				}
			}
		});
	},1000);

	// relocate error labels
	$labels.each(function(index){
		$(this).data('a11yc_error_index', index).next().attr('ariaLabel', this.title);
		relocate_labels(this);
	});
	/*
	$(window).on('resize', function(){
	});
	*/
	function relocate_labels(obj){
		var offset = $(obj).offset(),
		    left = offset.left,
		    top = offset.top-240;
		$(obj).appendTo('body').end().css({
			'left' : left,
			'top' : top
		});
	}

	// outline for hover|focus
	$labels.mouseenter(function(){
		var index = $(this).data('a11yc_error_index');
		$wrappers.eq(index).addClass('on').css({
			'outline' : '2px dashed #000',
			'outlineOffset' : '-2px',
			'opacity' : '.7'
		});
	}).mouseleave(function(){
		var index = $(this).data('a11yc_error_index');
		$wrappers.eq(index).removeClass('on').css({
			'outline' : '2px dashed #900',
			'opacity' : '1'
		});
	});

});


