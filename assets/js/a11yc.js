jQuery(function($){
if(!$('.a11yc')[0]) return;
//set grobal
	a11yc_env = {
		$a11yc_content : $('.a11yc').eq(0),
		menu_height : 0,
		pagemenu_height : 0,
		header_height : 0,
		top_padding : 0,
		scrollable_element : '',
		$menu : $('#wpadminbar')[0] ? $('#wpadminbar') : $('#a11yc_menu_wrapper'),
		$pagemenu : $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles') : $(),
		$pagemenu_count :$(),
		pagemenu_top : 0,
		current_position : 0,
		$current_level : $('[data-a11yc-target_level]').data('a11ycTarget_level'), //有無を調べてから？
		$additional_criterions: $(),
		is_hide_passed_item : $('.a11yc_hide_passed_item')[0] ? true : false,
		is_wp : $('.wp-admin')[0] ? true : false
	}

	//scrollable element
	a11yc_env.scrollable_element = (function(){
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

	if($('#a11yc_checks')[0]){
		// set addisional criterions
		var additional_arr = $('#a11yc_checks').data('a11ycAdditional_criterions');
		a11yc_env.$additional_criterions = $(additional_arr.map(function(el){ return '#a11yc_c_'+el }).join(','));
	}

	//ヘッダ下端（ページ内リンクのメニューの位置を取得？)
	$.fn.a11yc_get_menuposition = function(arr){
//	console.log('function:'+'$.fn.a11yc_get_menuposition');
		if($.inArray('all', arr) || $.inArray('mh', arr)) a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
		if($.inArray('all', arr) || $.inArray('pt', arr)) a11yc_env.pagemenu_top = a11yc_env.$pagemenu[0] ? a11yc_env.$pagemenu.offset().top - a11yc_env.menu_height : 0;
		if($.inArray('all', arr) || $.inArray('pmh', arr)) a11yc_env.pagemenu_height = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles').outerHeight() : 0;
		if($.inArray('all', arr) || $.inArray('hh', arr)) a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;


	}
	// get contents height
	$.fn.a11yc_get_menuposition(['all']);
});

/* === common functions === */
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
			if(!$obj.closest('#a11yc_header')[0]) return;
			$.fn.a11yc_get_menuposition ();
			if($t)
			{
				a11yc_smooth_scroll($t);
				$t.focus();
			}
		});
	}
});
jQuery(function($){
	//使用していない。validationのテキストのみの欄を作成。ソースをコピーする機能用
//	$.fn.a11yc_set_validation_code_txt = function(){
//	console.log('function:'+'$.fn.a11yc_set_validation_code_txt');
//		var $txt = $('#a11yc_validation_code_txt');
//		$txt.find(':not(br)').remove();
//	}
});
jQuery(function($){	
	//validationエラーの表示にページ内リンクなどを追加する。ので、表示時に一度だけ
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

		//エラー・ソース欄の展開用。これは外に追い出すといいかも
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
		}
	}
});

// display when javascript is active
jQuery(function($){
		$('.a11yc_hide_if_no_js').removeClass('a11yc_hide_if_no_js').addClass('a11yc_show_if_js');
		$('.a11yc_hide_if_no_js').find(':disabled').prop("disabled", false);
});

/* === title_tooltip === */
// replace title attr to aria-label.when element is link, screen reader speach out inner skip str.
jQuery(function($){
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


/* === smooth scroll === */
// links on the same page
// prepare
jQuery(function($){
	$(document).on('click', 'a[href^=#]', function(e){

		//ヘッダの中でやった時のことを考える
		e.preventDefault();
	 	var href = $(this).attr("href"),
				$t = $(href);
		if($t.closest($('#a11yc_menu, #a11yc_header'))[0]) return;
		if(href === '#') return; //今回使わないので？

		// add tabindex -1
		if( !$t.is(':input') && !$t.is('a') && !$t.attr('tabindex')) $t.attr('tabindex', '-1');

		if($('#a11yc_header')[0] && !$('.a11yc_fixed_header')[0])
		{
		console.log('fixdheaer_yuusen');
			$.when(a11yc_fixed_header(e))
			.done(a11yc_smooth_scroll(href));
		}
		else
		{
			setTimeout(function(){
				a11yc_smooth_scroll(href);
	//なかで
	//			$t.focus();
	//			return false; //要検討
			},50);
		}
	});
});
function a11yc_smooth_scroll(href) {
	console.time('a11yc_smooth_scroll');
	jQuery(function($){
		//重複をキャンセル
		$(a11yc_env.scrollable_element).stop();
		var $t = $(href),
			m  = 20, 
			t_position = $t[0] ? $t.offset().top : false,
			position = 0;
		if(t_position === false ) return;
		$.fn.a11yc_get_menuposition(['hh']);
		position = t_position - a11yc_env.header_height - m;
		
		//move
		$.when($(a11yc_env.scrollable_element).animate({scrollTop: position},500))
			.done($t.focus());
		return false; //要検討
	});
	console.timeEnd('a11yc_smooth_scroll');
}

/*
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
	$(a11yc_env.scrollable_element).animate({scrollTop: $t.offset().top-a11yc_headerheight-margin},500);
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
*/
/* === fixed_header === */
function a11yc_fixed_header(e){
jQuery(function($){
	if($('.a11yc_fixed_header')[0]) return;
//	$(a11yc_env.scrollable_element).stop();
//スクロール終了までフラグをたてて、どうじにうごかないようにするか、アニメーションを中断する
//	a11yc_env.$a11yc_content.addClass('a11yc_fixed_header_scroll');
	console.log('function:'+'a11yc_fixed_header');
	var position = $(window).scrollTop();
	var padding = a11yc_env.header_height+a11yc_env.menu_height;
	if (position >= a11yc_env.pagemenu_top)
	{
	console.time('a11yc_fixed_header');
/*		console.log('w_top: '+$(window).scrollTop());
		console.log('p_top: '+a11yc_env.pagemenu_top);
		console.log('h_height: '+a11yc_env.header_height);
*/
		$('#a11yc_header').find('.a11yc_hide_if_fixedheader').each(function(){
			if($(this).hasClass('hide')) return;
			$(this).removeClass('show').addClass('hide').hide();
			
			if(!$(this).hasClass('a11yc_disclosure_target')) return;
			var index = $('.a11yc_disclosure_target').index(this);
			$('.a11yc_disclosure').eq(index).removeClass('show').addClass('hide');
		});
		$.fn.a11yc_get_menuposition(['hh','mh','pmh']);

		a11yc_env.$a11yc_content.addClass('a11yc_fixed_header');
		if(!a11yc_env.is_wp)
		{
			a11yc_env.top_padding = a11yc_env.header_height+parseInt($('#a11yc_header_p_1').css('margin-top'), 10);
		}
		else
		{
			a11yc_env.top_padding = 0;
		}
/*		console.log('h_height: '+a11yc_env.header_height);
		console.log('pm_height: '+a11yc_env.pagemenu_height);
		console.log('t_padding: '+a11yc_env.top_padding);
		console.log('h_padding: '+a11yc_env.menu_height);
*/		
		// add padding for header space
		a11yc_env.$a11yc_content.css('paddingTop', a11yc_env.top_padding);
		$('#a11yc_header').css('paddingTop', a11yc_env.menu_height);
		
		//ページ内リンクのクリックで移動する場合は、こちらでは移動させない
		if(e.type=="click") return;
		var diff = padding-(a11yc_env.header_height+a11yc_env.pagemenu_height);
		console.log('fixed でscroll');
//		console.log(diff);
		
		// scroll by diff
		var moved_position = $(window).scrollTop();
		var adjust_position = moved_position-diff-a11yc_env.top_padding;
/*		console.log(' p: '+position);
		console.log('mp: '+moved_position);
		console.log('ap: '+adjust_position);
*/
		adjust_position = adjust_position < 1 ? 0 : adjust_position;
		$(a11yc_env.scrollable_element).scrollTop(adjust_position);
		
	console.timeEnd('a11yc_fixed_header');
	return;
/*
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
	$(a11yc_env.scrollable_element).scrollTop(position);
}

*/
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
	$.fn.a11yc_get_menuposition ();
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
		a11yc_narrow_level($(e.target), $($(this).parent().data('a11ycNarrowTarget')), e);
	});
	
	function a11yc_narrow_level($target, $narrow_target, e){
		if(!$target) return;
		var $checks = $narrow_target.find('.a11yc_level_a,.a11yc_level_aa,.a11yc_level_aaa');
		console.log('function:'+'a11yc_narrow_level');
		//eがないときは、ページ読み込み時eachで実行されているとき
		// stop propagation if in disclosure
		if(e && $target.closest('.a11yc_disclosure.show')[0]) e.stopPropagation();
		var level_arr = $target.data('narrowLevel') ? $target.data('narrowLevel') : [];
		//ここ、いつかlevel_arrの値の頭にl_がつかないようにして整理したい。むしろl_でいろいろちゃんと動くようにすべきか
		var $levels = $(level_arr.map(function(el){ return '.a11yc_leve'+el }).join(','));
		
		$checks.addClass('a11yc_dn');
		$levels.add(a11yc_env.$additional_criterions).removeClass('a11yc_dn');
	
		//validation_list only //これ、処理の分け方を考えたほうがよさそう
		if($narrow_target[0].id==='a11yc_validation_list')
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

//これもチェックリストのある場合、に隔離？
//count checkbox
function a11yc_count_checkbox(){
	var pid ='',
			additional_num = 0,
			$row= $(),
			count = 0,
			num = 0,
			subtotal = 0,
			total = 0;
	
	console.time('a11yc_count_checkbox');
	var levels_arr = ['a', 'aa', 'aaa'],
			count_arr = [[],[],[],[]],
			current_level = a11yc_env.$current_level.length;
	jQuery(function($){
		$('.a11yc_section_principle').each(function(index){
			pid = index+1;
			for(var i=0; i<3; i++)
			{
				count = 0;
				if(levels_arr[i].length <= current_level)
				{
					count = $(this).find('.'+levels_arr[i]).filter(':not(:disabled,:checked)').length;
				}
				else
				{
					a11yc_env.$additional_criterions.each(function(){
						if($(this).not('.a11yc_p_'+pid+'_criterion.a11yc_level_'+levels_arr[i])[0]) return;
						if($(this).find(':checkbox').not(':disabled, :checked')[0])
						{
							count++;
						}
					});
					count= count===0 ? ' - ' : count;
				}
				count_arr[index].push(count);
			}
		});
		for(var i=0; i<4; i++)
		{
			$row = $('#a11yc_rest_'+(i+1));
			num = 0;
			subtotal = 0;
			$row.find('td').each(function(index){
				if(index < 3)
				{
					num = count_arr[i][index];
					$(this).text(num);
					if( typeof num !=='number') return;
					subtotal+=num;
					return;
				}
			//subtotal
				$(this).text(subtotal);
				total+=subtotal;
				a11yc_env.$pagemenu_count.eq(i).text('('+subtotal+')');
			});
			$('#a11yc_rest_total').text(total);
		}
	});
	console.timeEnd('a11yc_count_checkbox');
}

// replace links in "source code"
function a11yc_validation_code_display(level_arr){
jQuery(function($){
	var $code = $('#a11yc_validation_code_raw'),
			$levels = $code.find(level_arr.map(function(el){ return '.a11yc_leve'+el }).join(',')),
			$objs = $code.find('.a11yc_validation_code_error, strong, a');
	$objs.addClass('a11yc_dn').attr('role', 'presentation');
	$levels.removeClass('a11yc_dn').removeAttr('role');
});
}

//hide empty table
function a11yc_empty_table(){
console.time('a11yc_empty_table');
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

	// addclass even/odd to visible tr
	$('.a11yc_table_check').each(function(){
		$(this).find('tr:not(.off)').each(function(index){
			$(this).removeClass('even odd');
			var class_str = index%2===0 ? 'odd' : 'even';
			$(this).addClass(class_str);
		});
	});
});
	console.timeEnd('a11yc_empty_table');
}



/* for pages */
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


/* for checklist */
jQuery(function($){
if(!$('#a11yc_checks')[0]) return;
	//いろんな描画を待って取得する値。これ、ヘッダとメニューの処理わけができるならfunctionにしたほうがらくかも
	setTimeout(function(){
		a11yc_env.header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;
		$.fn.a11yc_get_menuposition ();
		if (a11yc_env.$pagemenu[0])
		{
			if($('.a11yc_fixed_header')[0]) a11yc_env.pagemenu_top = a11yc_env.pagemenu_top - $(window).scrollTop();
		}
	},0);

	//動作補助
	// propagates click event from th to child checkbox
		$('#a11yc_checks th').on('click', function(e){
			if(e.target!==this) return;
			$(this).find(':checkbox').click();
		});
	//flash highlight
	$.fn.a11yc_flash = function(){
		$(this).addClass('a11yc_flash');
		setTimeout(function($obj){ $obj.removeClass('a11yc_flash') }, 150, $(this));
	}

//set chekclist contents
	a11yc_env.$pagemenu_count = a11yc_env.$pagemenu_count.add(a11yc_env.$pagemenu.find('span'));
	a11yc_count_checkbox();

//チェック関連の挙動
if($('.a11yc_table_check')[0])
{
	var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
	// toggle check items
	//ページ読み込み時にチェックの状態を反映
	a11yc_toggle_item();

	//チェックボックスクリック時
	$('.a11yc_table_check input[type="checkbox"]').on('click', function(e){
	// highlight ただし、今はtrにはa11yc_flashのスタイルがない
//		$(this).closest('tr').a11yc_flash();

	//チェックリストの表示制御
		a11yc_toggle_item(e);
		
	//チェックの数
		a11yc_count_checkbox();

	//非表示項目を非表示に
	a11yc_empty_table();

	// adjust position
	//あとで
	if (a11yc_env.is_hide_passed_item && $(window).scrollTop() !== 0)
	{
		console.time('adjust position');
		var movement_distance = a11yc_env.current_position - $(this).offset().top;
//		a11yc_env.current_position = $(this).offset().top;
// 何かのタイミングで、（ヘッダの高さが変わった時に）移動する場所が補正されないといけない
		$('body').scrollTop($(window).scrollTop()-movement_distance);
		console.timeEnd('adjust position');
	}

	
	// reflect current login user
	// メモの更新は？とくに、不適合理由にはチェックがないので
		var select = $(this).closest('tr').find('select');
		if(String(c_id) !== select.val()) select.val(c_id).a11yc_flash();
	});

/* 未使用。パスしたアイテムを隠すかどうかのチェックボックスがページ内にある場合。
	// show/hide passed items
	$('#a11yc_checklist_behaviour').on('click',function(){
		if($('#a11yc_checks')[0]){
			a11yc_env.is_hide_passed_item = a11yc_env.is_hide_passed_item ? false : true;
		}
	});
*/
}

	function a11yc_toggle_item(e){
//		console.log('function:'+'a11yc_toggle_item');
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
	


/*
fixedヘッダの扱いについて考える
・fixedにするタイミングを検知する（スクロール、a11yc内のページ内リンクのクリック）
・ヘッダの高さに合わせてpaddingを与える（fixedになった瞬間、resizeでヘッダの高さが変わった瞬間）
	・ヘッダ内でディスクロージャが開閉した時も？

・チェックした項目を隠す場合にはfixedに影響しない
・fixedを解除できるようにする？
*/
	// fixed header
	if ($('#a11yc_header')[0])
	{
		$(window).on('scroll', a11yc_fixed_header);
	}

/*
	$.fn.a11yc_get_menuposition ();
	a11yc_env.menu_height = a11yc_env.$menu.outerHeight();


// resize
$(window).on('resize', function(){
	if(!$('.a11yc_fixed_header')[0]) return;
	a11yc_env.menu_height = a11yc_env.$menu.outerHeight();
	a11yc_env.header_height = $('#a11yc_header').outerHeight();
	$.fn.a11yc_get_menuposition ();
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
		$(a11yc_env.scrollable_element).scrollTop(position);
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
	$.fn.a11yc_get_menuposition ();
}
*/
// docs narrow level
/*
if($('#a11yc_docs')[0])
{
}
*/
/*
// a11yc_table_check -- checklists, bulk





*/
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

/* === validation error_message === */
jQuery(function($){
	if(!$('.a11yc')[0]) return;
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
				if(!$('.a11yc_fixed_header')[0]) $.fn.a11yc_get_menuposition();
				$.fn.a11yc_format_validation_error();
				//$.fn.a11yc_set_validation_code_txt();
			},
			error:function() {
				$('#a11yc_errors').removeClass('a11yc_loading').text('failed');
			}
		});
	}
});


//使用していない
/*
function a11yc_select_validation_code_txt(){
//	console.log('function:'+'a11yc_select_validation_code_txt');
	var $code = $('#a11yc_validation_code_raw');
	var $txt = $('#a11yc_validation_code_txt');
	var range = document.createRange();
	range.selectNodeContents($txt[0]);
	window.getSelection().addRange(range);
}
*/

/* === disclosure === */
jQuery(function($){
	var $disclosure = $(),
			$disclosure_target = $();
	$disclosure = $(document).find('.a11yc_disclosure');
	$disclosure_target = $(document).find('.a11yc_disclosure_target');
	$.fn.a11yc_disclosure();
	
	$(document).on('click keydown', '.a11yc_disclosure',  function(e){
		if(e && e.type==='keydown' && e.keyCode!==13) return;
		$.fn.a11yc_disclosure_toggle($(this));
		
		// ヘッダーがある場合はメニュー位置を取得し直したほうがよさそうなので。使う関数はあとで調整
		if($('.a11yc_fixed_header')[0]) $.fn.a11yc_get_menuposition();
	});
});

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

/* === assist === */
/*
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
	$(a11yc_env.scrollable_element).animate({scrollTop: $t.offset().top-a11yc_headerheight-margin},500);
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
		$(a11yc_env.scrollable_element).scrollTop(position);
	},100);
}
*/
