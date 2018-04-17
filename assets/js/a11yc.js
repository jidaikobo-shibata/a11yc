/*
 * * * なにをやるjsか * * *
 * ツールチップの表示
 * ページ内スクロール(ヘッダの高さを計算する)
  * ページ読み込み時にすでにチェックされているものの表示反映
 * レベルの絞り込み
 * ページスクロール時に固定ヘッダに変更 position:stickyでよさそう? 疑似クラスが実装されないと中身の要素の表示・非表示を切り替えづらい
 * 追加して達成する項目の管理
 * ドロワー表示・非表示
 * Ajax：バリデーションエラーメッセージの取得*不要
 * チェック時のcode部分の表示調整
 */
jQuery(function($){
if(!$('.a11yc')[0])
{
	return;
}
//set grobal
	window.a11yc_env = {
		$a11yc_content : $('.a11yc').eq(0),
		$footer : $('#a11yc_submit')[0] ? $('#a11yc_submit') : $(),
		fixed_height : 0,
		menu_height : 0,
		pagemenu_height : 0,
		pagemenu_top : 0,
		header_height : 0,
		fixed_header_height : 0,
		header_position: 0,
		top_padding : 0,
		scrollable_element : '',
		$menu : $('#wpadminbar')[0] ? $('#wpadminbar') : $('#a11yc_menu_wrapper'),
		$pagemenu : $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles') : $(),
		current_position : 0,
		$current_level : $('[data-a11yc-target_level]')[0] ? $('[data-a11yc-target_level]').data('a11ycTarget_level') : $(),
		$additional_criterions: $(),
		is_hide_passed_item : $('.a11yc_hide_passed_item')[0] ? true : false,
		is_wp : $('.wp-admin')[0] ? true : false
	}

	//get contents height
	$.fn.a11yc_get_height = function(arr){
//	console.log('fn.a11yc_get_height');
//	console.log(typeof arr!=='undefined' ? arr : 'get all');
		if(!arr || $.inArray('mh', arr))
		{
			a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
			$('#a11yc_header').css('top', a11yc_env.menu_height );
		}
		if(!arr || $.inArray('pt', arr))
		{
			a11yc_env.pagemenu_top = a11yc_env.$pagemenu[0] ? a11yc_env.$pagemenu.offset().top - a11yc_env.menu_height : 0;
		}
		if(!arr || $.inArray('pmh', arr))
		{
			a11yc_env.pagemenu_height = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles').outerHeight() : 0;
		}
		if(!arr || $.inArray('hh', arr))
		{
			a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight(true) : 0;
		}
		if(!arr || $.inArray('fh', arr))
		{
			a11yc_env.fixed_height = $('.a11yc_fixed_header')[0] ? $('#a11yc_header').outerHeight(true) : a11yc_env.$menu.outerHeight();
		}
		if(( !arr || $.inArray('ff', arr)) && a11yc_env.fixed_footer_top !==0 )
		{
			a11yc_env.fixed_footer_top = a11yc_env.$footer[0] ? a11yc_env.$footer.offset().top : a11yc_env.fixed_footer_top;
		}
	}
	// get contents height all
	$.fn.a11yc_get_height();
	
	// set header default position
	(function get_header_position(){
		if($('#a11yc_header')[0])
		{
			if($('#a11yc_msg_info')[0])
			{
				a11yc_env.header_position = $('#a11yc_msg_info').offset().top +  $('#a11yc_msg_info').outerHeight();
			}
			else
			{
				a11yc_env.header_position = $('h1').eq(1).offset().top +  $('h1').eq(1).outerHeight();
			}
			a11yc_env.header_position =  a11yc_env.header_position + parseInt($('#a11yc_header').closest('form').css('margin-top')) ;
		}
	}());
	

	// get scrollable element
	window.a11yc_env.scrollable_element = (function(){
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

});

/* === common functions === */
// display when javascript is active
jQuery(function($){
	$('.a11yc_hide_if_no_js').removeClass('a11yc_hide_if_no_js').addClass('a11yc_show_if_js');
	$('.a11yc_hide_if_no_js').find(':disabled').prop("disabled", false);
});
/* === confirm === */
jQuery(function($){
	//確認ウィンドウ
	$('[data-a11yc-confirm]').on('click', function(e){
		if(!window.confirm($(this).data('a11ycConfirm')))
		{
			e.preventDefault();
			return false;
		}
	});
});

jQuery(function($){
	//ディスクロージャ
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
			// ヘッダーの中の場合はposition:stickyをon/off
			if($(this).closest('#a11yc_header')[0])
			{
				if($(this).hasClass('show'))
				{
					a11yc_env.$a11yc_content.removeClass('a11yc_fixed_header');
					$('#a11yc_header').css('position', 'static');
				}
				else
				{
//					a11yc_env.$a11yc_content.addClass('a11yc_fixed_header');
					$('#a11yc_header').css('position', 'sticky');
				}
			}
			
		// ヘッダーの中の場合はメニュー位置を取得し直したほうがよさそう？
		});
	}
	var $disclosure = $(),
		$disclosure_target = $();
	$disclosure = $(document).find('.a11yc_disclosure');
	$disclosure_target = $(document).find('.a11yc_disclosure_target');
	$.when(
		$.fn.a11yc_disclosure()
	).done(function(){
	// ヘッダーの中の場合はメニュー位置を取得し直したほうがよさそう？
		a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight(true) : 0;
	});


	$(document).on('click keydown', '.a11yc_disclosure',  function(e){
		if(e && e.type==='keydown' && e.keyCode!==13) return;
		$.fn.a11yc_disclosure_toggle($(this));
	});
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

		if($('#a11yc_header')[0] && !$('.a11yc_fixed_header')[0])
		{
			$.when(a11yc_fixed_header(e))
			.done(function(){
				a11yc_smooth_scroll(href)
			});
		}
		else
		{
			a11yc_smooth_scroll(href);
		}
	});
});
function a11yc_smooth_scroll(href) {
//	console.time('a11yc_smooth_scroll');
	jQuery(function($){
		if(!$('.a11yc')[0]) return;
		//If already scrolling, stop scroll and start from that position
		$(a11yc_env.scrollable_element).stop();
		var $t = $(href),
			t_position = $t[0] ? $t.offset().top : false,
			position = 0;
		if(t_position === false ) return;

		//move
		if(a11yc_env.$a11yc_content.hasClass('a11yc_fixed_header'))
		{
			position = t_position - a11yc_env.fixed_header_height;
		}
		else
		{
			position = t_position - ( $('#a11yc_header').offset().top - a11yc_env.menu_height + a11yc_env.header_height );
		}
		$.when($(a11yc_env.scrollable_element).animate({scrollTop: position + 3 },500))
			.done($t.focus());
		return false; //
	});
//	console.timeEnd('a11yc_smooth_scroll');
}

/* for checklist */

/* === fixed_header === */
jQuery(function($){
	// fixed header
	if ($('#a11yc_header')[0])
	{
		$(window).on('scroll', a11yc_fixed_header);
	}

	// resize
	$(window).on('resize', function(){
		$.fn.a11yc_get_height();
		//本当はresizeの際にpaddingも変化させないといけない
		//	a11yc_fixed_header();
	});
}) ;
function a11yc_fixed_header(e){
	//console.time('a11yc_fixed_header');
	jQuery(function($){
		if(! $('#a11yc_header')[0]) return;
		if($('#a11yc_header').offset().top > a11yc_env.header_position)
		{
			a11yc_env.$a11yc_content.addClass('a11yc_fixed_header');
			if(! a11yc_env.fixed_header_height )
			{
				a11yc_env.fixed_header_height = a11yc_env.menu_height + $('#a11yc_header').height();
			}
		}
		else
		{
			a11yc_env.$a11yc_content.removeClass('a11yc_fixed_header');
		}
	//	console.timeEnd('a11yc_fixed_header');
	});
}

/* === narrow level === */
jQuery(function($){
	// load
	$('.a11yc_narrow_level').each(function(){
		a11yc_narrow_level( $(this).find('.current'), $($(this).data('a11ycNarrowTarget')));
	});
	// click
	$(document).on('click', '.a11yc_narrow_level a', function(e){
		a11yc_narrow_level($(this), $($(this).parent().data('a11ycNarrowTarget')), e);
	});

	function a11yc_narrow_level($target, $narrow_target, e){
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
		if($narrow_target.attr('id')==='a11yc_validation_error_list')
		{
//			a11yc_validation_code_display(level_arr);
		}

		$target.parent().find('a').removeClass('current');
		$target.addClass('current');
	}
});

jQuery(function($){
if(!$('#a11yc_checks')[0]) return;
	// thのクリックを子孫のチェックボックスに伝播
		$('#a11yc_checks th').on('click', function(e){
			if(e.target!==this) return;
			$(this).find(':checkbox').click();
		});
	// 選択時に行の背景に色をつける。animateでもうすこし整理できるかも。
	$.fn.a11yc_flash = function(){
		$(this).addClass('a11yc_flash');
		setTimeout(function($obj){ $obj.removeClass('a11yc_flash') }, 150, $(this));
	}
	
	//チェック関連の挙動
	if($('.a11yc_table_check')[0])
	{
		var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
		// toggle check items
		//ページ読み込み時にチェックの状態を反映
//		a11yc_toggle_item();

		//click checkbox
		$('.a11yc_table_check input[type="checkbox"]').on('click', function(e){
		});
	}
	function a11yc_toggle_item(e){
	}

});

/* === bulk === */
jQuery(function($){
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

/* === pages === */
// 閉じられていないdivが相手なので、いったん後回し。
// auto scroll for pages
/*var a11yc_load_url, scroll_target;
function a11yc_auto_scroll(){
	scroll_target = document.getElementById('a11yc_pages_scroll');
	console.log(scroll_target);
	a11yc_load_url = setInterval(function(){
		scroll_target.scrollTop = scroll_target.scrollHeight;
	}, 100);
}
*/
function a11yc_stop_scroll(){
//	scroll_target.scrollTo(0,document.body.scrollHeight);
//	clearInterval(a11yc_load_url);
}
