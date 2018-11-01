jQuery(function($){
if(!$('.a11yc')[0])
{
	return;
}
//set grobal
	window.a11yc_env = {
		$a11yc_content : $('.a11yc').eq(0),
		fixed_height : 0,
		scroll_top_position : 0,
		$footer : $('#a11yc_submit')[0] ? $('#a11yc_submit') : $(),
		fixed_footer_top : $('#a11yc_submit')[0] ? $('#a11yc_submit').offset().top : 0 ,
		menu_height : 0,
		pagemenu_height : 0,
		pagemenu_top : 0,
		current_position : 0,
		scrollable_element : '',
		$menu : $('#wpadminbar')[0] ? $('#wpadminbar') : $('#a11yc_menu_wrapper'),
		$pagemenu : $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles') : $(),
		$current_level : $('[data-a11yc-target_level]').data('a11ycTarget_level'), //有無を調べてから？
		$additional_criterions: $(),
		is_hide_passed_item : $('.a11yc_hide_passed_item')[0] ? true : false,
		is_wp : $('.wp-admin')[0] ? true : false
	}

	//scrollable element
	a11yc_env.scrollable_element = (function(){
		var $html = $('html'),
		    top = $html.scrollTop(),
		    $el = $('<div>').height(10000).prependTo('body'),
		    rs = false;
		$html.scrollTop(10000);
		rs = !!$html.scrollTop();
		rs = rs ? 'html' : 'body';
		$html.scrollTop(top);
		$el.remove();
		return rs;
	}());

	// set addisional criterions
	if($('#a11yc_checks')[0]){
		var additional_arr = $('#a11yc_checks').data('a11ycAdditional_criterions');
		a11yc_env.$additional_criterions = $(additional_arr.map(function(el){ return '#a11yc_c_'+el }).join(','));
	}

	//get contents height
	$.fn.a11yc_get_height = function(arr){
		// height : menu (toolbar)
		if(!arr || $.inArray('mh', arr))
		{
			a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
		}
		// top : menu_principles
		if(!arr || $.inArray('pt', arr))
		{
			a11yc_env.pagemenu_top = a11yc_env.$pagemenu[0] ? a11yc_env.$pagemenu.offset().top - a11yc_env.menu_height : 0;
		}
		// height : menu_principles
		if(!arr || $.inArray('pmh', arr))
		{
			a11yc_env.pagemenu_height = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles').outerHeight() : 0;
		}
		// height : a11yc_header
		if(!arr || $.inArray('hh', arr))
		{
			a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight(true) : 0;
		}
		// top of fixed footer
		if(( !arr || $.inArray('ff', arr)) && a11yc_env.fixed_footer_top !==0 )
		{
			a11yc_env.fixed_footer_top = a11yc_env.$footer.offset().top;
		}
	}
	$.fn.a11yc_get_height();
});

/*
 *  common functions
 */
 
/*=== display when javascript is active === */
jQuery(function($){
	$('.a11yc_hide_if_no_js').removeClass('a11yc_hide_if_no_js').addClass('a11yc_show_if_js');
	$('.a11yc_hide_if_no_js').find(':disabled').prop("disabled", false);
});

/* === title_tooltip === */
// replace title attr to aria-label.when element is link, screen reader speach out inner skip str.
jQuery(function($){
	if( typeof a11yc_env === "undefined" ) return;

	a11yc_tooltip();
	function a11yc_tooltip(){
//	console.log('function:'+'a11yc_tooltip');
		var $a11yc_tooltip = $(),
				title_str = '',
				position = 0,
				top = 0,
				left = 0,
				right = 0;

		$a11yc_tooltip = $('<span id="a11yc_tooltip" aria-hidden="true" role="presentation"></span>').hide().appendTo('body');

		$('.a11yc').on({
			'mouseenter focus': function(e){
				if(!$(this).is('a, span, :input, strong')) return;
				setTimeout(function($obj){
					title_str = $obj.attr('title');
					position = $obj.offset();
					$a11yc_tooltip.text(title_str).stop(true, true).show();
					$obj.data('a11ycTitle', title_str).attr('ariaLabel', title_str).removeAttr('title');

					//position
					$a11yc_tooltip.css('top', position.top-5-$a11yc_tooltip.outerHeight()+'px');
					$a11yc_tooltip.css('left', position.left-$a11yc_tooltip.outerWidth()/2+'px');
					top = position.top-5-$a11yc_tooltip.outerHeight();
					top = top-$(window).scrollTop()<0 ? position.top+$obj.outerHeight()+5 : top;
					left = $a11yc_tooltip.offset().left;
					left = left < 0 ? 0 : left;
					right = $(window).outerWidth()-left-$a11yc_tooltip.outerWidth();
					left = right < 0 ? left + right : left;

					$a11yc_tooltip.css({'top': top+'px', 'left': left+'px'});
				}
				, 0, $(this));
			},
			'mouseleave blur': function(e){
				$a11yc_tooltip.fadeOut('10', function(){
					$(this).css({'top': '-1em', 'left': '.5em'});
				});
				$(this).attr('title',$(this).data('a11ycTitle')).removeAttr('ariaLabel');
			}
		},'[title], [ariaLabel]');
	}
});

/* ===  adjust focus position === */
jQuery(function($){
	if( typeof a11yc_env === "undefined" ) return;
	$('.a11yc').on('focus', 'a, :input', function(e){
		e.stopPropagation();
		setTimeout(function(){
			var $t = $(e.target);
			if(!$t[0] || $t.closest($('#a11yc_menu, #a11yc_header, #a11yc_submit'))[0]) return;
			var scroll = $(window).scrollTop();
			var h_position = scroll+a11yc_env.header_height;
			var t_position = $t.offset().top;
			if(h_position > t_position) //hidden in fixed header
			{
				$(a11yc_env.scrollable_element).scrollTop(t_position-a11yc_env.header_height);
			}
			else if($t.closest('#a11yc_header')[0]) //hidden in fixed menu
			{
				var $t_parent = $t.closest('#a11yc_validation_list, .a11yc_source');
//				if(!$t_parent[0])
//				{
//				}
			}
			else //if(  )//hidden in fixed footer
			{
//					var f_position = a11yc_env.fixed_footer_top;
			}
		},100);
	});

	$.fn.a11yc_adjust_position = function($obj){
	if(!$obj) return;
//		console.time('fn.a11yc_adjust_position');
		var diff = a11yc_env.current_position - $obj.offset().top;
		$(a11yc_env.scrollable_element).scrollTop($(window).scrollTop()-diff);
//		console.timeEnd('fn.a11yc_adjust_position');
	}
});

/* === smooth scroll === */
// links on the same page
// prepare
jQuery(function($){
	if( typeof a11yc_env === "undefined" ) return;
	$(document).on('click', 'a[href^=#]', function(e){
		var href = $(this).attr("href"),
				$t = $(href);
		// return if target is in header
		if($t.closest($('#a11yc_menu, #a11yc_header'))[0] || href === '#') return;
		e.preventDefault();

		// add tabindex -1
		if( !$t.is(':input') && !$t.is('a') && !$t.attr('tabindex')) $t.attr('tabindex', '-1');
		a11yc_smooth_scroll(href);
	});
});
function a11yc_smooth_scroll(href) {
	jQuery(function($){
		if(!$('.a11yc')[0]) return;
		//If already scrolling, stop scroll and start from that position
		console.log(a11yc_env.header_height);
		
		$(a11yc_env.scrollable_element).stop();
		var $t = $(href),
			t_position = $t[0] ? $t.offset().top : false,
			position = 0;
		if(t_position === false ) return;

		//move
		position = t_position - ( a11yc_env.header_height + a11yc_env.menu_height );
		$.when($(a11yc_env.scrollable_element).animate({scrollTop: position},500))
			.done($t.focus());
		return false; //
	});
}

/* === confirm === */
jQuery(function($){
	$('[data-a11yc-confirm]').on('click', function(e){
		if(!window.confirm($(this).data('a11ycConfirm')))
		{
			e.preventDefault();
			return false;
		}
	});
});

/* === check all === */
jQuery(function($){
// check all
	$('.a11yc_check_all, .a11yc_table thead input:checkbox').on('click', function(){
		var $trigger = $(this), $target = $(), index, prop;
		if( $trigger.attr('dataA11ycTargetClass') )
		{
			$target = $('.'+$trigger.attr('dataA11ycTargetClass'));
		}
		else
		{ // thead内の場合は、あえてclassを与えなくてもtbody内の対応するセルのinputを取得する
			index = $trigger.closest('tr').children().index($trigger.closest('th, td'));
			$trigger.attr('dataA11ycTargetClass', 'a11yc_check_all_target_'+index);
			$trigger.closest('table').find('tbody tr').each(function(){
				$(this).children().eq(index).find('input:checkbox').addClass('a11yc_check_all_target_'+index);
				$target = $target.add($(this).children().eq(index).find('input:checkbox'));
			});
		}
		if( !$target[0] )
		{
			return;
		}
		prop = $trigger.prop('checked');
		$target.prop('checked', prop);
	});
});


/* === narrow level === */
jQuery(function($){
	if( typeof a11yc_env === "undefined" ) return;
	// load
	$('.a11yc_narrow_level').each(function(){
		a11yc_narrow_level( $(this).find('.current'), $($(this).data('a11ycNarrowTarget')));
	});
	// click
	$(document).on('click', '.a11yc_narrow_level a', function(e){
		e.stopPropagation();
		if( $(this).closest('details')[0]  &&  $(this).closest('details').prop('open') === false )
		{
			 $(this).closest('details').prop('open', true);
		}
		a11yc_narrow_level($(this), $($(this).parent().data('a11ycNarrowTarget')), e);
		return false;
	});

	function a11yc_narrow_level($target, $narrow_target, e){
//	console.log($target);
		if(!$target) return;
		var $checks = $narrow_target.find('.a11yc_level_a,.a11yc_level_aa,.a11yc_level_aaa');
//		console.log('function:'+'a11yc_narrow_level');
		// no e (page loading) or in disclosure , stop propagation
		if(e && $target.closest('.a11yc_disclosure.show')[0]) e.stopPropagation();
		var level_arr = $target.data('narrowLevel') ? $target.data('narrowLevel') : [];
		//ここ、いつかlevel_arrの値の頭にl_がつかないようにして整理したい。むしろl_でいろいろちゃんと動くようにすべきか
		var $levels = $(level_arr.map(function(el){ return '.a11yc_leve'+el }).join(','));

		$checks.addClass('a11yc_dn');
		$levels.add(a11yc_env.$additional_criterions).removeClass('a11yc_dn');

		//validation_list only //これ、処理の分け方を考えたほうがよさそう
		if( $narrow_target[0] && $narrow_target[0].id==='a11yc_validation_list')
		{
			a11yc_validation_code_display(level_arr);
		}

		//この条件も要検討
		if($narrow_target.hasClass('a11yc_section_principle'))
		{
			//empty table
			a11yc_empty_table();
		}

		$target.parent().find('a').removeClass('current');
		$target.addClass('current');
	}
});

/* === add link anchor etc in validation error === */
jQuery(function($){
	$.fn.a11yc_format_validation_error = function(){
		var $error_wrapper = $('#a11yc_validation_list');
		if ($error_wrapper[0])
		{
			var $error_lists = $error_wrapper.find('dt');
			var $error_elms = $error_wrapper.find('.a11yc_validation_error_str');
			var $error_anchors = $('#a11yc_validation_code').find('.a11yc_source span');
			var $error_places = $();
			var $validation_code = $('#a11yc_validation_code');
			
			// click validate_link
			$(document).on('click', '.a11yc_validate_link a', function(e){
				var $t = $($(e.currentTarget).attr('href'));
				e.stopPropagation();
				e.preventDefault();
				// open disclosure
				if( ! $validation_code.prop('open'))
				{
					$validation_code.prop('open');
					$(e.currentTarget).click();
				}
				else
				{
					a11yc_smooth_scroll($t);
					$t.focus();
				}
				return false;
			});
		}
	}
});

/* ===  replace links in "source code"  === */
function a11yc_validation_code_display(level_arr){
jQuery(function($){
	var $code = $('#a11yc_validation_code_raw'),
			$levels = $code.find(level_arr.map(function(el){ return '.a11yc_leve'+el }).join(',')),
			$objs = $code.find('.a11yc_validation_code_error, strong, a');
	$objs.addClass('a11yc_dn').attr('role', 'presentation');
	$levels.removeClass('a11yc_dn').removeAttr('role');
});
}

/* === hide empty table === */
function a11yc_empty_table(){
//console.time('a11yc_empty_table');
jQuery(function($){
//	console.log('function:'+'a11yc_empty_table');
	if(!a11yc_env.is_hide_passed_item) return;

	// hide disuse items
	$('.a11yc form').find('.a11yc_section_guideline, .a11yc_table_check').each(function(){
		var $t = !$(this).is('table') ? $(this) : $(this).closest('.a11yc_section_criterion');

		if (!$(this).find('tr:not(.off)')[0]) // 見えているものがない場合
		{
				$t.hide();
		}
		else
		{
			if(!$t.hasClass('a11yc_dn')) $t.show();
		}
	});
});
//	console.timeEnd('a11yc_empty_table');
}

/*
 * for checklist
 */
jQuery(function($){
	if(!$('#a11yc_checks')[0]) return;
	/* === assist === */
	//チェック関連の挙動
	if($('.a11yc_table_check')[0])
	{
		var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
		// toggle check items
		//ページ読み込み時にチェックの状態を反映
		a11yc_toggle_item();

		//click checkbox
		$('.a11yc_table_check input[type="checkbox"]').on('click', function(e){
			// display
			a11yc_toggle_item(e);

			// for not used items
			a11yc_empty_table();

			// adjust position
			if (a11yc_env.is_hide_passed_item)
			{
				$.fn.a11yc_adjust_position($(this))
			}
		});
	}
	function a11yc_toggle_item(e){
//		console.log('function:'+'a11yc_toggle_item');
		var input = e ? $(e.target) : '',
				input_name = input ? $(input).attr('name').replace('[','\\[').replace(']','\\]') : '',
				$same_name = input ? $(document).find("[name="+input_name+"]:not(#"+input.id+")") : $(),
		    $checked = $('.a11yc_table_check th :checked'),
		    data_pass_arr = [],
		    $show_items = $();
		if(!input) //ページ読み込み時
		{
			a11yc_set_pass_items($checked);
		}
		else
		{
			//位置調整用。チェックした行の表示がずれないように位置を取得しておく
			a11yc_env.current_position = input.offset().top;

			if(input.prop('checked'))
			{
				a11yc_set_pass_items(input);
			}
			else //チェックが外されたとき
			{
				a11yc_set_pass_items($checked, input);
			}
			// 同じnameのアイテムにも同じ値を反映
			$same_name.prop('checked', input.prop('checked'))
		}
	}
	function a11yc_set_pass_items($target, $passed){
//	console.log('function:'+'a11yc_set_pass_items');
		var $show_items = $();
		$pass_items = a11yc_set_passes($target);
		//$passedがなければ、passする処理
		if(!$passed){
			$pass_items.closest('tr').addClass('off').find(':input').prop("disabled", true);
			return;
		}else{
		//$passed があれば、パスしなくなったものを表示して終了
			$not_pass_items = a11yc_set_passes($passed);
			$not_pass_items.each(function(){
				// パスするものの中にあれば除外
				if($pass_items[0] && $pass_items.index(this) !== -1 ) return;
				$show_items = $show_items.add(this);
			});
			$show_items = $pass_items[0] ? $show_items : $not_pass_items;
			$show_items.closest('tr').removeClass('off').find(':input').prop("disabled", false);
		}
	}
		function a11yc_set_passes($target){
//	console.log('function:'+'a11yc_set_passes');
		if(!$target) return ;
		var $items = $();
		$target.each(function(){
			data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
			for(var k in data_pass_arr)
			{
				if({}.hasOwnProperty.call(data_pass_arr, k))
				{
					if(data_pass_arr[k]===this.id) continue; //自分自身は相手にしない？
					$items = $items.add('#'+data_pass_arr[k]);
				}
			}
		});
		return $items;
	}

	// resize
	$(window).on('resize', function(){
		$.fn.a11yc_get_height();
		//本当はresizeの際にpaddingも変化させないといけない
		//	a11yc_fixed_header();
	});
});

/* === bulk === */
jQuery(function($){
	if( typeof a11yc_env === "undefined" ) return;
	if(!$('.a11yc')[0]) return;

	// a11yc_update_done
	$('#a11yc_update_done').parent().addClass('a11yc_hide');
	$('#a11yc_update_all').on('change', function(){
		if($(this).val() > 1)
		{
			$('#a11yc_update_done').parent().removeClass('a11yc_hide').attr('aria-hidden', false);
		}
		else
		{
			$('#a11yc_update_done').parent().addClass('a11yc_hide').attr('aria-hidden', true);
		}
	});
});



/* === pages === */
// auto scroll for pages
var a11yc_load_url;
function a11yc_auto_scroll(){
	a11yc_load_url = setInterval(function(){
		window.scrollTo(0,document.body.scrollHeight);
	}, 100);
}
function a11yc_stop_scroll(){
	window.scrollTo(0,document.body.scrollHeight);
	clearInterval(a11yc_load_url);
}


/* === get validation error_message === */
/*
jQuery(function($){
	if(!$('.a11yc')[0] || $('#a11yc_post')[0] ) return;
	if(!a11yc_env.is_wp && $('#a11yc_validator_results')[0]){
		$.ajax({
			type: 'POST',
			url: $('#a11yc_validator_results').data('a11ycAjaxUrl'),
			dataType: 'html',
			data: {
				url: $('#a11yc_validator_results').data('a11ycUrl'),
				link_check: $('#a11yc_validator_results').data('a11ycLinkCheck')
			},
			beforeSend: function() {
				$('#a11yc_validator_results').addClass('a11yc_loading');
			},
			success: function(data) {
				$('#a11yc_validator_results').removeClass('a11yc_loading').append(data);
//				$.fn.a11yc_disclosure();
				$.fn.a11yc_format_validation_error();
				//$.fn.a11yc_set_validation_code_txt();
			},
			error:function() {
				$('#a11yc_validator_results').removeClass('a11yc_loading').text('failed');
			}
		});
	}
});
*/
//not used
/*
jQuery(function($){
	//validationのテキストのみの欄を作成。ソースをコピーする機能用
	$.fn.a11yc_set_validation_code_txt = function(){
//	console.log('function:'+'$.fn.a11yc_set_validation_code_txt');
		var $txt = $('#a11yc_validation_code_txt');
		$txt.find(':not(br)').remove();
	}
});
function a11yc_select_validation_code_txt(){
//	console.log('function:'+'a11yc_select_validation_code_txt');
	var $code = $('#a11yc_validation_code_raw');
	var $txt = $('#a11yc_validation_code_txt');
	var range = document.createRange();
	range.selectNodeContents($txt[0]);
	window.getSelection().addRange(range);
}
*/


