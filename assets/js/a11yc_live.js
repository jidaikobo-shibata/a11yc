if( typeof jQuery == 'undefined' )
{
	// load jQuery
	var script = document.createElement("SCRIPT");
	script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
	script.type = 'text/javascript';
	script.onload = function(){
		a11yc_js();
	};
	document.getElementsByTagName("head")[0].appendChild(script);
}
else
{
	a11yc_js();
}

function a11yc_js(){
	jQuery(function($){
		// set position base
		if($('body').css('position') == 'static')
		{
			$('body').css('position', 'relative');
		}
	
		var $labels = $('.a11yc_validation_code_error'),
		    $wrappers = $('.a11yc_live_error_wrapper');

		// error text
		$labels.each(function(){
			$('<span class="a11yc_error_text a11yc_live" />').text($(this).attr('title')).appendTo(this);
		});
		
		// alt
		$('img').each(function(){
			$(this).wrap('<span class="a11yc_live_img_wrapper a11yc_live">');
			var error_msg = "Missing Alt attribute !!!";
			var alt_str = $(this).attr('alt');
			if( typeof alt_str == 'undefined' )
			{ // missing alt ( insert error tip !!! )
				$('<span class="a11yc_live_alt a11yc_live_alt_not_exsit a11yc_live" title="'+error_msg+'">').text(error_msg).insertBefore(this);
			}
			else
			{
				// alt exist
				$('<span class="a11yc_live_alt a11yc_live" title="'+alt_str+'" />').text('alt="'+alt_str+'"').insertBefore(this);
			}
		});
		
		//tabindex
		setTimeout(function(){
			$(document).find('[tabindex]:not(.a11yc_validation_code_error)').removeAttr('tabindex');
			$(document).find('a, :input').attr('tabindex', -1).on('click', function(e){
				var e = e ? e : event;
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
		}, 1000);
		$labels.attr('tabindex', 0);
		
		// set wrapper position
		/*
		$wrappers.each(function(){
			var height = $(this).height();
			if( height == 0 )
			{
				$(this).addClass('a11yc_live_noheight');
			}
		});
		*/
		$(window, 'iframe').on('load', function(){
			$wrappers.each(set_wrapper_position);
		});
		function set_wrapper_position(){
		// need relocate wrapper for changing objs height by load images

			
//			if(! $(this).hasClass('a11yc_live_noheight')) return;
			if( $(this).is(':empty') ) $(this).next().wrap(this);
			var offset,
			    left,
			    top,
			    height = $(this).outerHeight(),
			    width = $(this).outerWidth(),
			    index = $wrappers.index(this);
			    title_str = $labels.eq(index).attr('title'),
			    class_str = $(this).hasClass('a11yc_live_notice') ? ' a11yc_live_notice' : '';
			    
			if( height != 0 )
			{
				if( $(this).css('display', 'block').height() > height )
				{
					height = 0;
				}
				else
				{
						offset = $(this).offset();
						left = offset.left;
						top = offset.top;
					}
				$(this).css('display', 'inline');
			}

			if( height == 0 )
			{
				$(this).find(':not(".a11yc_live")').each(function(){
					if($(this).height() != 0)
					{
						height = $(this).outerHeight();
						width = $(this).outerWidth();
						offset = $(this).offset();
						left = offset.left;
						top = offset.top;
					}
				});
			}
			$('<span class="a11yc_live_error_outline a11yc_live'+class_str+'" title="'+title_str+'" />').css({
//				'height': height , 
				'position' : 'absolute',
				'display' : 'block',
				'height' :  height+'px',
				'width' : width+'px',
				'left' : left,
				'top' : top
			}).appendTo('body');
//			$(this).removeClass('a11yc_live_error_outline');
		}
	
		// relocate error labels
		$labels.each(function(index){
			var $wrapper = $(this).next();
			$(this).data('a11yc_error_index', index).next().attr('aria-label', this.title);
			if($(this).hasClass('a11yc_validation_code_notice')){
				$wrapper.addClass('a11yc_live_notice_wrapper');
			}
			a11yc_relocate($(this), $wrapper);
		});
		
		// for iframes
		// add overlay and relocate labels
		$('iframe').on('load', function(){
			//  overlay for iframes
			$obj = $('<span class="a11yc_overlay a11yc_live" role="presentation" />').css({
				'width'  : $(this).width() +'px',
				'height' : $(this).height()+'px',
			});
			a11yc_relocate($obj, $(this));
			
			// relocate labels 
			$labels.each(function(){
				var $wrapper = $wrappers.eq($(this).data('a11yc_error_index'));
				$(this).css({
					'top'  : 'auto',
					'left' : 'auto'
				}).insertBefore($wrapper);
				a11yc_relocate($(this), $(this).next());
			});
		});
	
		/*
		// catch window resize
		$(window).on('resize', function(){
		});
		*/
	
		function a11yc_relocate($obj, $parent){
			var $parent = $parent ? $parent : $obj,
			    offset = $parent.offset(),
			    left = offset.left,
			    top = offset.top;
			if($obj.is($labels))
			{
				top = top + 16;
			}
			$obj.css({
				'left' : left,
				'top' : top
			}).appendTo('body');
		}
	
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
		location_path = window.location.pathname;
		location_path = location_path.substring(0,location_path.lastIndexOf('/'));
		location_path = location_origin+location_path;
		location_target = decodeURIComponent(arg.base_url);
		if(!location_target) return;
		setTimeout(function(){
			$(document).find('*').each(function(){
				$obj = $(this);
				var needle = '';
				if( $obj.css('backgroundImage') != 'none')
				{
					var url = /^url\((['"]?)(.*)\1\)$/.exec($(this).css('backgroundImage'));
					if(! url ) return;
					url = url[2];
					if (url.indexOf(location_origin) == 0)
					{
						needle = url.indexOf(location_path) == 0 ? location_path : location_origin;
						url = url.replace(location_origin,  location_target);
						$obj.css('backgroundImage', 'url("'+url+'")');
					}
				}
				else if( $obj[0].src)
				{
					var url = $obj[0].src;
					if (url.indexOf(location_origin) == 0)
					{
						needle = url.indexOf(location_path) == 0 ? location_path : location_origin;
						url = url.replace(needle,  location_target);
						$obj.attr('src', url);
					}
				}
			});
			
		//	relocate wrappers and labels
			$wrappers.each(set_wrapper_position);
			$labels.each(function(index){
				var $wrapper = $wrappers.eq(index);
				a11yc_relocate($(this), $wrapper);
			});
		},1000);

		
	
		// outline for hover|focus
		$labels.mouseenter(function(){
			$wrappers.eq($(this).data('a11yc_error_index')).addClass('a11yc_live_on');
		}).mouseleave(function(){
			$wrappers.eq($(this).data('a11yc_error_index')).removeClass('a11yc_live_on');
		});
	
	});
}