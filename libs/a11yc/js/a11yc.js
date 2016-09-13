jQuery(function($){
	var $a11yc_content = $(),
			menu_height = 0,
			header_height = 0,
			$menu = $(),
			$pagemenu = $(),
			$pagemenu_count =$(),
			pagemenu_top = 0,
			current_position = 0,
			current_distance = 0,
			$info = $(),
			$current_level = '',
			num = 0,
			$checked = $(),
			data_pass_arr = [],
			$pass_items = $(),
			$show_items = $(),
			$show_items2 = $();

	$a11yc_content = $('.a11yc').eq(0);
	$menu = $('#wpadminbar')[0] ? $('#wpadminbar') : $('#a11yc_menu ul');
	$pagemenu = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles') : $pagemenu;

	setTimeout(function(){
		menu_height = $menu.outerHeight();
		header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;
		if ($pagemenu[0])
		{
			pagemenu_top = $('.a11yc_fixed_header')[0] ? $pagemenu.offset().top - $(window).scrollTop() : pagemenu_top;
		}
	},0);
	if($pagemenu[0])
		{
			$pagemenu.find('a').each(function(index){
				var $obj = $('<span></span>').appendTo(this);
				$pagemenu_count = $pagemenu_count.add($obj);
			});
		}

// resize
$(window).on('resize', function(){
	menu_height = $menu.outerHeight();
	header_height = $('#a11yc_header').outerHeight();
//	pagemenu_top = pagemenu_top>0 ? $pagemenu.offset().top - menu_height : pagemenu_top;
	pagemenu_top = $('.a11yc_fixed_header')[0] ? pagemenu_top - $(window).scrollTop() : pagemenu_top;
	a11yc_fixed_header();
});


// fixed header
if ($('#a11yc_header')[0])
{
	$(window).on('scroll', a11yc_fixed_header);
}

function a11yc_fixed_header(){
	if ($(window).scrollTop() > pagemenu_top)
	{
		$a11yc_content.addClass('a11yc_fixed_header');
		if(!$('.wp-admin')[0])
		{
			$a11yc_content.css('paddingTop', menu_height);
		}
		else
		{
			$a11yc_content.css('paddingTop', 0);
		}
		$('#a11yc_header').css('paddingTop', menu_height);
	}
	else
	{
		$a11yc_content.removeClass('a11yc_fixed_header');
		if(!$('.wp-admin')[0])
		{
			$a11yc_content.css('paddingTop', menu_height);
		}
		else
		{
			$a11yc_content.css('paddingTop', 0);
		}
		$('#a11yc_header').css('paddingTop', 0);
	}
}

	
// checklists, bulk
if($('.a11yc_table_check')[0])
{
	$info = $('#a11yc_rest');

	// レベルを絞り込み
	a11yc_nallow_level();
	$('#a11yc_narrow_level a').on('click', a11yc_nallow_level);
	
	function a11yc_nallow_level(e){
		var $target = e ? $(e.target) : $('#a11yc_narrow_level .current');
		if(!$target[0]) $target = $('#a11yc_narrow_level a').eq(-1);
		$current_level = $target.text();
		var data_levels = $target.data('narrowLevel') ? $target.data('narrowLevel').split(',') : [];
		var $show_levels = $();
		$target.parent().find('a').removeClass('current');
		$target.addClass('current');
		for (var k in data_levels)
		{
//			$show_levels = $show_levels.add($('.'+data_levels[k]));
			$show_levels = $show_levels.add($('[data-a11yc-level ='+data_levels[k]+']'));
		}
		$('.a11yc_section_criterion').addClass('a11yc_dn');
		$show_levels.removeClass('a11yc_dn');
		
		//テーブルの表示調整
		a11yc_table_display();
		//カウント
		a11yc_count_checkbox();
	}

	//チェック項目の表示・非表示切り替え
	a11yc_toggle_item();
	$('.a11yc_table_check input[type="checkbox"]').on('click', a11yc_toggle_item);
	
	function a11yc_toggle_item(e){
		var input = e ? $(e.target) : '';
		$checked = $('.a11yc_table_check th :checked');

		if(!input) //ページ読み込み時
		{
			$checked.each(function(){
				data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
				for(var k in data_pass_arr)
				{
					if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
					$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
				}
			});
			$pass_items.closest('tr').addClass('off').find(':input').prop("disabled", true);
		}
		else
		{
			//位置調整用。チェックした行の表示がずれないように位置を取得しておく
			current_position = input.offset().top;
			if(input.prop('checked')) // チェックされたとき
			{
				data_pass_arr = input.data('pass') ? input.data('pass').split(',') : [];
				for(var k in data_pass_arr)
				{
					if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
					$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
				}
				$pass_items.closest('tr').addClass('off').find(':input').prop("disabled", true);
			}
			else //チェックが外されたとき
			{
				data_pass_arr = input.data('pass') ? input.data('pass').split(',') : [];
				$show_items = $();
				$pass_items = $();
				$show_items2 = $();
				for(var k in data_pass_arr)
				{
					if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない
					$show_items = $show_items.add('#'+data_pass_arr[k]);
				}
				$checked.each(function(){
					data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
					for(var k in data_pass_arr)
					{
						if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
						$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
					}
				});
//ここまで、pass_itemsは大丈夫
				$show_items.each(function(){
					// 閉じるべきものの中にないものだけ開く
					if($pass_items[0] && $pass_items.index(this) != -1 ) return;
					$show_items2 = $show_items2.add(this);
				});
				$show_items2 = $pass_items[0] ? $show_items2 : $show_items;
				$show_items2.closest('tr').removeClass('off').find(':input').prop("disabled", false);

			}
		}
		//テーブルの表示調整
		a11yc_table_display();
		//カウント
		a11yc_count_checkbox();
	}
	
	//table display
	function a11yc_table_display(){
		if(!$('.a11yc_hide_passed_item')[0]) return;
		//不要な項目を隠す
		$('.a11yc form').find('.a11yc_section_guideline, .a11yc_table_check').each(function(){
			var $t = !$(this).is('table') ? $(this) : $(this).closest('.a11yc_section_criterion');
			
			if (!$(this).find('tr:not(.off)')[0]) //見えているものがない場合
			{
//				$t.addClass('a11yc_dn');
					$t.hide();
			}
			else
			{
//				$t.removeClass('a11yc_dn');
				if(!$t.hasClass('a11yc_dn')) $t.show();
			}
		});

		//表示されているtrのeven/odd
		if ($('.a11yc_hide_passed_item')[0])
		{
			$('.a11yc_table_check').each(function(){
				if (!$(this).find('tr:not(.off)')[0]) return;
				$(this).find('tr:not(.off)').each(function(index){
					$(this).removeClass('even odd');
					if (index%2==0){
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
		num = $('.a11yc_table_check tr:visible th input:not(:checked)').length;
		$info.find('thead td').text(num);
		var n = 0, subtotal = 0, total = 0, n_str = '', num_arr = [];
		$('#a11yc_rest tbody tr').each(function(index){
			subtotal = 0;
			var pid = '#a11yc_p_'+(index+1);
			$(this).find('td').each(function(index){
				if(!$(this).is('.a11yc_rest_subtotal')){
					var l_str = '';
					for(var i=0; i<=index; i++) l_str= l_str+'a'; 
					n_str = $(pid).find('[data-a11yc-level=l_'+l_str+'] th input').filter(':not(:disabled,:checked)').length;

					if (index+1 <= $current_level.length)
					{
						subtotal = subtotal+n_str;
					}
					else
					{
							n_str = '-';
					}
					$(this).text(n_str);
				}
				else
				{
					$(this).text(subtotal);
				}
			});
			total = total+subtotal;
			$pagemenu_count.eq(index).text('('+subtotal+')');
		});
		$('#a11yc_rest_total').text(total);
	}

	// 現在のログインユーザを反映
	var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
	$('#a11yc_checks :checkbox').on('click', function(){
		var select = $(this).closest('tr').find('select');
		if(c_id!=select.val()) select.val(c_id).a11yc_flash();
	});
	// チェックボックスクリック時の強調と、位置調整
	$('#a11yc_checks :checkbox').on('click', function(){
		$(this).closest('tr').a11yc_flash();

		if (!$('.a11yc_hide_passed_item')[0] || $(window).scrollTop()==0) return;
		var movement_distance = current_position - $(this).offset().top;
//		current_position = $(this).offset().top;
// 何かのタイミングで、（ヘッダの高さが変わった時に）移動する場所が補正されないといけない
		$('body').scrollTop($(window).scrollTop()-movement_distance);
	});
}
// パスした項目の表示・非表示
$('#a11yc_checklist_behaviour').on('click',function(){
	if($('#a11yc_checks')[0]) $('#a11yc_checks').toggleClass('a11yc_hide_passed_item');
});


/* === bulk === */
// a11yc_update_done
$('#a11yc_update_done').parent().addClass('a11yc_dn');
$('#a11yc_update_all').on('change', function(){
	if($(this).val() > 1)
	{
		$('#a11yc_update_done').parent().removeClass('a11yc_dn');
	}
	else
	{
		$('#a11yc_update_done').parent().addClass('a11yc_dn');
	}
});

/* disclosure */
var $disclosure = $('.a11yc_disclosure');
var $disclosure_target = $('.a11yc_disclosure_target');
$disclosure.attr('tabindex', 0).each(function(index){
	$(this).addClass('active');
	if ($disclosure_target.eq(index).hasClass('show'))
	{
		$(this).addClass('show');
	}
	else
	{
		$(this).addClass('hide')
	}
});
$disclosure_target.each(function(){
	if (!$(this).hasClass('show')) $(this).addClass('hide').hide();
});
$disclosure.on('click', function(){
	var index = $(this).index('.a11yc_disclosure');
	$(this).toggleClass('show hide');
	$disclosure_target.eq(index).slideToggle(250).toggleClass('show hide');
});

/* === 動作補助 === */

//ページ内リンクでのスクロール
var is_html_scrollable = (function(){
	var $html, $el, rs, top;
	$html = $('html');
	top = $html.scrollTop();
	$el = $('<div>').height(10000).prependTo('body');
	$html.scrollTop(10000);
	rs = !!$html.scrollTop();
	$html.scrollTop(top);
	$el.remove();
	return rs;
})();

// ページ内リンク
$(document).on('click', 'a[href^=#]', function(e){
	e = e ? e : event;
	e.preventDefault();
 	var href, $t, position;
	href= $(this).attr("href");
	if (href!='#')
	{
		$t = href != '' ? $(href) : $('html');
		// ターゲットがフォーカス対象になってない場合はtabindex-1を付与する
		if( !$t.is(':input') && !$t.is('a') && !$t.attr('tabindex')) $t.attr('tabindex', '-1');
		setTimeout(function(){
			a11yc_smooth_scroll($t);
			$t.focus();
			return false;
		},50);
	}
});

function a11yc_smooth_scroll($t) {
	var position, margin, a11yc_headerheight;
	if($t.closest($('#a11yc_menu, #a11yc_header'))[0]) return;
	position = $t.offset();
	if(typeof position === 'undefined') return;
	a11yc_headerheight = $('#a11yc_menu').height()+$('#a11yc_header').height();
	margin = 40;
	position = position.top-$(window).scrollTop()-a11yc_headerheight;
	$(is_html_scrollable ? 'html' : 'body').animate({scrollTop: $t.offset().top-a11yc_headerheight-margin},500);
}

//一時的なハイライト
$.fn.a11yc_flash = function(){
	$(this).addClass('a11yc_flash');
	setTimeout(function($obj){ $obj.removeClass('a11yc_flash') }, 150, $(this));
	//消えるのもふわっとしたい
}

//thのクリックをチェックボックスに伝播
$('#a11yc_checks th').on('click', function(e){
	if(e.target!=this) return;
	$(this).find(':checkbox').click();
});


//JavaScript有効時に表示、無効時にはCSSで非表示
	$('.a11yc_hide_if_no_js').removeClass('a11yc_hide_if_no_js').addClass('a11yc_show_if_js');
	$('.a11yc_hide_if_no_js').find(':disabled').prop("disabled", false);
});
