jQuery(function($){
//set grobal
	a11yc_env = {
		$a11yc_content : $(),
		menu_height : 0,
		header_height : 0,
		top_padding : 0,
		$menu : $(),
		$pagemenu : $(),
		$pagemenu_count :$(),
		pagemenu_top : 0,
		current_position : 0,
		$info : $(),
		$current_level : '',
		$additional_criterions: $(),
		is_wp : $('.wp-admin')[0] ? true : false
	}
});

jQuery(function($){
//common functions
	$.fn.a11yc_set_pagemenu_top = function(){
//	console.log('function:'+'$.fn.a11yc_set_pagemenu_top');
		a11yc_env.pagemenu_top = a11yc_env.$pagemenu[0] ? a11yc_env.$pagemenu.offset().top - a11yc_env.menu_height : 0;
		a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;
	}

	$.fn.a11yc_disclosure = function(){
//	console.log('function:'+'$.fn.a11yc_disclosure');
		$disclosure = $(document).find('.a11yc_disclosure');
		$disclosure_target = $(document).find('.a11yc_disclosure_target');
		$disclosure.each(function(index){
			if($(this).hasClass('active')) return;
			$(this).attr('tabindex', 0).addClass('active');
			if ($disclosure_target.eq(index).hasClass('show'))
			{
				$(this).addClass('show');
			}
			else
			{
				$(this).addClass('hide');
			}
		});
		$disclosure_target.each(function(){
			if($(this).hasClass('active')) return;
			$(this).addClass('active');
			if (!$(this).hasClass('show')) $(this).addClass('hide').hide();
		});
	}
	$.fn.a11yc_disclosure_toggle = function($obj, $t){
//	console.log('function:'+'$.fn.a11yc_disclosure_toggle');
		if(!$obj) return;
		var index = $obj.index('.a11yc_disclosure');
		$obj.toggleClass('show hide');
		$.when(
			$disclosure_target.eq(index).slideToggle(250).toggleClass('show hide')
		).done(function(){
			if(!$obj.closest('#a11yc_header')[0]) return;
			$.fn.a11yc_set_pagemenu_top ();
			if($t)
			{
				a11yc_smooth_scroll($t);
				$t.focus();
			}
		});
	}
	$.fn.a11yc_set_validation_code_txt = function(){
//	console.log('function:'+'$.fn.a11yc_set_validation_code_txt');
		var $txt = $('#a11yc_validation_code_txt');
		$txt.find(':not(br)').remove();
	}

	$.fn.a11yc_format_validation_error = function(){
//	console.log('function:'+'$.fn.a11yc_format_validation_error');

		var $error_wrapper = $('#a11yc_validation_list');
		if ($error_wrapper[0])
		{
			var $error_lists = $error_wrapper.find('dt');
			var $error_elms = $error_wrapper.find('.a11yc_validation_error_str');
			var $error_anchors = $('#a11yc_validation_code').find('.a11yc_source span');
			var $disclosure = $('#a11yc_validation_code').find('.a11yc_source');
			var $error_places = $();
			var $controller = $('#a11yc_errors .a11yc_controller');

			//expand contents
			var icon_labels = [$('#a11yc_checks').data('a11ycLang').expand, $('#a11yc_checks').data('a11ycLang').compress];
			$expand_icon = $('<a role="button" class="a11yc_expand a11yc_hasicon" tabindex="0"><span role="presentation" aria-hidden="true" class="a11yc_icon_fa a11yc_icon_expand"></span><span class="a11yc_skip">'+icon_labels[0]+'</span></a>');

			$expands = $error_wrapper.add($disclosure);
			$controller.append($expand_icon.clone());

			$(document).on('click', '.a11yc_expand', function(){
				var index = $('.a11yc_expand').index(this);
				$(this).toggleClass('on');
				$expands.eq(index).toggleClass('expand');
				if($(this).hasClass('on')){
					$(this).find('.a11yc_skip').text(icon_labels[1]);
				}else{
					$(this).find('.a11yc_skip').text(icon_labels[0]);
				}
			});
			// click validate_link
			$(document).on('click', '.a11yc_validate_link a', function(e){
				var $t = $($(e.currentTarget).attr('href'));
				e.stopPropagation();
				e.preventDefault();
				// open disclosure
				if($disclosure.hasClass('hide'))
				{
				 $.when($.fn.a11yc_disclosure_toggle($disclosure, $(e.currentTarget)))
				 .done(function(){
						$(e.currentTarget).click();
				 });
				}
				else
				{
					a11yc_smooth_scroll($t);
					$t.focus();
				}
				return false;
			});
			// narrow level
		}
	}

});
jQuery(function($){
	a11yc_env.$a11yc_content = $('.a11yc').eq(0);
	var scrollable_element = (function(){
		var $html, $el, rs, top;
		$html = $('html');
		top = $html.scrollTop();
		$el = $('<div>').height(10000).prependTo('body');
		$html.scrollTop(10000);
		rs = !!$html.scrollTop();
		rs = rs ? 'html' : 'body';
		$html.scrollTop(top);
		$el.remove();
		return rs;
	}());
	a11yc_env.$menu = $('#wpadminbar')[0] ? $('#wpadminbar') : $('#a11yc_menu_wrapper');
	a11yc_env.$pagemenu = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles') : a11yc_env.$pagemenu;
	if($('#a11yc_checks')[0]){
		var additional_arr = $('#a11yc_checks').data('a11ycAdditional_criterions').split(',');
		for( var k in additional_arr ){
			if(!{}.hasOwnProperty.call(additional_arr, k)) continue;
			a11yc_env.$additional_criterions = a11yc_env.$additional_criterions.add('#a11yc_c_'+additional_arr[k]);
		}
	}
	$.fn.a11yc_set_pagemenu_top ();

	setTimeout(function(){
		a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
		a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;
		$.fn.a11yc_set_pagemenu_top ();
		if (a11yc_env.$pagemenu[0])
		{
			if($('.a11yc_fixed_header')[0]) a11yc_env.pagemenu_top = a11yc_env.pagemenu_top - $(window).scrollTop();
		}
	},0);
	if(a11yc_env.$pagemenu[0])
		{
			a11yc_env.$pagemenu.find('a').each(function(index){
				var $obj = $('<span></span>').appendTo(this);
				a11yc_env.$pagemenu_count = a11yc_env.$pagemenu_count.add($obj);
			});
		}

// resize
$(window).on('resize', function(){
	if(!$('.a11yc_fixed_header')[0]) return;
	a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
	a11yc_env.header_height = $('#a11yc_header').outerHeight();
	$.fn.a11yc_set_pagemenu_top ();
	a11yc_env.pagemenu_top = a11yc_env.pagemenu_top - $(window).scrollTop();
	a11yc_fixed_header();
});


// fixed header
if ($('#a11yc_header')[0])
{
	$(window).on('scroll', a11yc_fixed_header);
}

function a11yc_add_fixed() {
//	console.log('function:'+'a11yc_add_fixed');

		var scroll = $(window).scrollTop();
		if($('.a11yc_fixed_header')[0]) return;
		//ここで#a11yc_headerがないばあいがある？？
		a11yc_env.header_height = a11yc_env.header_height!==0 ? $('#a11yc_header').outerHeight(true)+$('#a11yc_header').offset().top : 0;
//		if(!a11yc_env.$a11yc_content.hasClass('a11yc_add_fixed')){
			$(document).find('.a11yc_hide_if_fixedheader').each(function(){
				if($(this).hasClass('hide')) return;
				$(this).removeClass('show').addClass('hide').hide();
				
				if(!$(this).hasClass('a11yc_disclosure_target')) return;
				var index = $('.a11yc_disclosure_target').index(this);
				$('.a11yc_disclosure').eq(index).removeClass('show').addClass('hide');
			});
//		}
		a11yc_env.$a11yc_content.addClass('a11yc_add_fixed')
		a11yc_env.$a11yc_content.addClass('a11yc_fixed_header');
//		$('#a11yc_header_ctrl').prependTo('#a11yc_header');
		if(!a11yc_env.is_wp)
		{
			a11yc_env.top_padding = $('#a11yc_header').outerHeight()+(parseInt($('#a11yc_header_p_1').css('margin-top'), 10))*2;
		}
		else
		{
			a11yc_env.top_padding = 0;
		}
		a11yc_env.$a11yc_content.css('paddingTop', a11yc_env.top_padding);
		$('#a11yc_header').css('paddingTop', a11yc_env.menu_height);
		//この移動量をもう少し考える
		var position = scroll-a11yc_env.header_height+$('#a11yc_header').outerHeight()-$('#a11yc_menu_principles').outerHeight();
		position = position < 1 ? 1 : position;
		$(scrollable_element).scrollTop(position);
}
function a11yc_fixed_header(e){
	console.log('function:'+'a11yc_fixed_header');
	if ($(window).scrollTop() >= a11yc_env.pagemenu_top)
	{
		a11yc_add_fixed();
	}

//	remove
	if($(window).scrollTop() === 0)
	{
		if(!$('.a11yc_fixed_header')[0]) return;
		a11yc_env.$a11yc_content.removeClass('a11yc_fixed_header');
		$('#a11yc_header_ctrl').prependTo('#a11yc_form_checklist');
		if(!a11yc_env.is_wp)
		{
			a11yc_env.$a11yc_content.css('paddingTop', a11yc_env.menu_height);
		}
		else
		{
			a11yc_env.$a11yc_content.css('paddingTop', 0);
		}
		$('#a11yc_header').css('paddingTop', 0);
	}
	$.fn.a11yc_set_pagemenu_top ();
}

// docs narrow level
/*
if($('#a11yc_docs')[0])
{
}
*/
// a11yc_table_check -- checklists, bulk
//eがないときは、ページ読み込み時
function a11yc_narrow_level(target_narrow, index, e){
//	console.log('function:'+'a11yc_narrow_level');
	if(e && e.type==='keydown' && e.keyCode!==13) return;
	if(e && $(e.target).parent().hasClass('show')) e.stopPropagation();
	var $target = e ? $(e.target) : $('.a11yc_narrow_level').eq(index).find('.current');
	var $target_narrow = $(target_narrow);
	a11yc_env.$current_level = $target.text();
	var data_levels = $target.data('narrowLevel') ? $target.data('narrowLevel').split(',') : [];
	var $show_levels = $();
	$target.parent().find('a').removeClass('current');
	$target.addClass('current');

	for(var k in data_levels)
	{
		if({}.hasOwnProperty.call(data_levels, k)){
			$show_levels = $show_levels.add($target_narrow.find('.a11yc_leve'+data_levels[k]));
		}
	}
	$target_narrow.find('.a11yc_level_a,.a11yc_level_aa,.a11yc_level_aaa').addClass('a11yc_dn');
	$show_levels.add(a11yc_env.$additional_criterions).removeClass('a11yc_dn');


	//validation_list only
	if(target_narrow==='#a11yc_validation_list')
	{
		a11yc_validation_code_display(data_levels);
	}
	//checklist only
	if(target_narrow==='.a11yc_section_principle')
	{
		//table display
		a11yc_table_display();
		//count
		a11yc_count_checkbox();
	}
}
function a11yc_validation_code_display(data_levels){
//	console.log('function:'+'a11yc_validation_code_display');
	var $code = $('#a11yc_validation_code_raw');
	var $show_levels = $();
	for( var k in data_levels )
	{
		if({}.hasOwnProperty.call(data_levels, k))
		{
			$show_levels = $show_levels.add($code.find('.a11yc_leve'+data_levels[k]));
		}
	}
	$code.find('.a11yc_validation_code_error, strong, a').addClass('a11yc_dn').attr('role', 'presentation');
	$show_levels.removeClass('a11yc_dn').removeAttr('role');
}
function a11yc_set_passes($target){
//	console.log('function:'+'a11yc_set_passes');
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
function a11yc_toggle_item(e){
//	console.log('function:'+'a11yc_toggle_item');
	var $checked = $(),
			data_pass_arr = [],
			$show_items = $(),
			$show_items2 = $();
	var input = e ? $(e.target) : '';
	$checked = $('.a11yc_table_check th :checked');

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
	}
	a11yc_table_display();
	a11yc_count_checkbox();
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
//table display
function a11yc_table_display(){
//	console.log('function:'+'a11yc_table_display');
	if(!$('.a11yc_hide_passed_item')[0]) return;
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

	// addclass even/odd to visible tr
	if ($('.a11yc_hide_passed_item')[0])
	{
		$('.a11yc_table_check').each(function(){
			if (!$(this).find('tr:not(.off)')[0]) return;
			$(this).find('tr:not(.off)').each(function(index){
				$(this).removeClass('even odd');
				if (index%2===0){
					$(this).addClass('odd');
				}
				else
				{
					$(this).addClass('even');
				}
			});
		});
	}
}

//count checkbox
function a11yc_count_checkbox(){
	var additional_num = 0, subtotal = 0, total = 0, l_str='', n_str = '', num_arr = [];
	$('#a11yc_rest tbody tr').each(function(index){
		subtotal = 0;
		var pid = '#a11yc_p_'+(index+1);
		$(this).find('td').each(function(col_index){
			additional_num = 0;
			if(!$(this).is('.a11yc_rest_subtotal')){
				l_str = '';
				for(var i=0; i<=col_index; i++) l_str= l_str+'a';
				n_str = $(pid).find('.a11yc_level_'+l_str+' th input').filter(':not(:disabled,:checked)').length;
				if (col_index+1 <= a11yc_env.$current_level.length)
				{
					subtotal = subtotal+n_str;
				}
				else
				{
					n_str = '-';
					additional_num = a11yc_env.$additional_criterions.filter('.a11yc_level_'+l_str+'.a11yc_p_'+(index+1)+'_criterion').length;
					if(additional_num!==0)
					{
						n_str = additional_num;
						subtotal = subtotal+n_str;
					}
				}
				$(this).text(n_str);
			}
			else
			{
				$(this).text(subtotal);
			}
		});
		total = total+subtotal;
		a11yc_env.$pagemenu_count.eq(index).text('('+subtotal+')');
	});
	$('#a11yc_rest_total').text(total);
}

if($('.a11yc_table_check')[0])
{
	// narrow level
	$('.a11yc_narrow_level').each(function(index){
		a11yc_narrow_level($(this).data('a11ycNarrowTarget'), index);
	});
	$(document).on('click keydown', '.a11yc_narrow_level a', function(e){
		var index = $('.a11yc_narrow_level').index($(this).parent());
		a11yc_narrow_level($(this).parent().data('a11ycNarrowTarget'), index, e);
	});

	// toggle check items
	a11yc_toggle_item();
	$('.a11yc_table_check input[type="checkbox"]').on('click', a11yc_toggle_item);

	// reflect current login user
	var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
	$('#a11yc_checks :checkbox').on('click', function(){
		var select = $(this).closest('tr').find('select');
		if(String(c_id) !== select.val()) select.val(c_id).a11yc_flash();
	});
	// highlight/adjust position when checkbox is clicked
	$('#a11yc_checks :checkbox').on('click', function(){
		$(this).closest('tr').a11yc_flash();

		if (!$('.a11yc_hide_passed_item')[0] || $(window).scrollTop() === 0) return;
		var movement_distance = a11yc_env.current_position - $(this).offset().top;
//		a11yc_env.current_position = $(this).offset().top;
// 何かのタイミングで、（ヘッダの高さが変わった時に）移動する場所が補正されないといけない
		$('body').scrollTop($(window).scrollTop()-movement_distance);
	});

	// show/hide passed items
	$('#a11yc_checklist_behaviour').on('click',function(){
		if($('#a11yc_checks')[0]) $('#a11yc_checks').toggleClass('a11yc_hide_passed_item');
	});
}

/* === bulk === */
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

/* === validation error_message === */
if(!a11yc_env.is_wp && $('#a11yc_errors')[0]){
	$.ajax({
		type: 'POST',
		url: $('#a11yc_errors').data('a11ycAjaxUrl'),
		dataType: 'html',
		data: {
			url: $('#a11yc_errors').data('a11ycUrl'),
			link_check: $('#a11yc_errors').data('a11ycLinkCheck')
		},
		beforeSend: function() {
			$('#a11yc_errors').addClass('a11yc_loading');
		},
		success: function(data) {
			$('#a11yc_errors').removeClass('a11yc_loading').append(data);
			$.fn.a11yc_disclosure();
			if(!$('.a11yc_fixed_header')[0]) $.fn.a11yc_set_pagemenu_top ();
			$.fn.a11yc_format_validation_error();
			$.fn.a11yc_set_validation_code_txt();
		},
		error:function() {
			$('#a11yc_errors').removeClass('a11yc_loading').text('failed');
		}
	});
}

function a11yc_select_validation_code_txt(){
//	console.log('function:'+'a11yc_select_validation_code_txt');
	var $code = $('#a11yc_validation_code_raw');
	var $txt = $('#a11yc_validation_code_txt');
	var range = document.createRange();
	range.selectNodeContents($txt[0]);
	window.getSelection().addRange(range);
}

/* === disclosure === */
var $disclosure = $(),
		$disclosure_target = $();
$disclosure = $(document).find('.a11yc_disclosure');
$disclosure_target = $(document).find('.a11yc_disclosure_target');
$.fn.a11yc_disclosure();

$(document).on('click keydown', '.a11yc_disclosure',  function(e){
	if(e && e.type==='keydown' && e.keyCode!==13) return;
	$.fn.a11yc_disclosure_toggle($(this));
});

/* === confirm === */
$('[data-a11yc-confirm]').on('click', function(e){
	if(!window.confirm($(this).data('a11ycConfirm')))
	{
		e.preventDefault();
		return false;
	}
});

/* === assist === */

// smooth scroll
// links on the same page
// prepare
$(document).on('click', 'a[href^=#]', function(e){
	e.preventDefault();
 	var href,
			$t,
			position;
	href = $(this).attr("href");
	if(href === '#') return;
	$t = $(href);
	// add tabindex -1
	if( !$t.is(':input') && !$t.is('a') && !$t.attr('tabindex')) $t.attr('tabindex', '-1');

	setTimeout(function(){
		a11yc_smooth_scroll($t);
		$t.focus();
		return false; //要検討
	},50);
});

function a11yc_smooth_scroll($t) {
//	console.log('function:'+'a11yc_smooth_scroll');
	var position,
			margin,
			a11yc_headerheight;

	if($t.closest($('#a11yc_menu, #a11yc_header'))[0]) return;
	position = $t.offset();
	if(typeof position === 'undefined') return;
	a11yc_headerheight = $('#a11yc_menu ul').height()+$('#a11yc_header').height();
	margin = 40;
	position = position.top-$(window).scrollTop()-a11yc_headerheight;
	if($('#a11yc_header')[0])
	{
		a11yc_add_fixed();
	}
	$(scrollable_element).animate({scrollTop: $t.offset().top-a11yc_headerheight-margin},500);
}

$('.a11yc').on('keydown', function(e){
	if( e.which!==9 ) return;
	setTimeout(function(){
		if($(':focus')[0])
		{
			a11yc_adjust_position($(':focus'));
		}
	},0);
});

function a11yc_adjust_position($obj) {
//	console.log('function:'+'a11yc_adjust_position');
	if($obj.closest('#a11yc_menu , #a11yc_header')[0] ) return;
	setTimeout(function(){
		var a11yc_position_header_bottom = $('#a11yc_header')[0] ? $('#a11yc_header').offset().top+$('#a11yc_header').outerHeight() : 0;
		if($obj.offset().top >= a11yc_position_header_bottom) return;
		var position = $(window).scrollTop()-(a11yc_position_header_bottom-$obj.offset().top)-30;
		position = position < 1 ? 1 : position;
		$(scrollable_element).scrollTop(position);
	},100);
}

//flash highlight
$.fn.a11yc_flash = function(){
	$(this).addClass('a11yc_flash');
	setTimeout(function($obj){ $obj.removeClass('a11yc_flash') }, 150, $(this));
}

// propagates click event from th to child checkbox
$('#a11yc_checks th').on('click', function(e){
	if(e.target!==this) return;
	$(this).find(':checkbox').click();
});

// display when javascript is active
	$('.a11yc_hide_if_no_js').removeClass('a11yc_hide_if_no_js').addClass('a11yc_show_if_js');
	$('.a11yc_hide_if_no_js').find(':disabled').prop("disabled", false);

// title_tooltip
// replace title attr to aria-label.when element is link, screen reader speach out inner skip str.
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
