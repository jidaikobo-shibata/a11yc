jQuery(function($){
	var $a11yc_content = $(),
			menu_height = 0,
			header_height = 0,
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
	menu_height = $('#a11yc_menu ul').outerHeight();
	header_height = $('#a11yc_header')[0] ? $('#a11yc_header').outerHeight() : 0;
	pagemenu_top = $('#a11yc_menu_principles')[0] ? $('#a11yc_menu_principles').offset().top - menu_height : 0;
// resize
$(window).on('resize', function(){
	menu_height = $('#a11yc_menu ul').outerHeight();
	header_height = $('#a11yc_header').outerHeight();
	pagemenu_top = pagemenu_top>0 ? $('#a11yc_menu_principles').offset().top - menu_height : pagemenu_top;
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
	}
	else
	{
		$a11yc_content.removeClass('a11yc_fixed_header');
	}
	if ($a11yc_content.hasClass('a11yc_fixed_header'))
	{
		$a11yc_content.css('paddingTop', menu_height+header_height+30);//あとでヘッダの高さ等調整が利くようにする
		$('#a11yc_header').css('paddingTop', menu_height);
	}
	else
	{
		$a11yc_content.css('paddingTop', menu_height);
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
			$show_levels = $show_levels.add($('[data-a11yc-lebel ='+data_levels[k]+']'));
		}
		$('.a11yc_section_criterion').hide();
		$show_levels.show();
		
		//テーブルの表示調整
		a11yc_table_display();
		//カウント
		a11yc_count_checkbox();
	}

	//チェック項目の表示・非表示切り替え
	a11yc_toggle_item();

	$(':checkbox').on('click', a11yc_toggle_item);
	function a11yc_toggle_item(e){
		var input = e ? $(e.target) : '';
		if(!input) $checked = $(':checked');

		if(!input) //ページ読み込み時
		{
			$checked = $(':checked');
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
		else if(input.prop('checked')) // チェックされたとき
		{
			//チェックボックスの位置を取得
			current_position = $(this).offset().top;
			current_distance = current_distance-$(window).scrollTop();

			data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
			for(var k in data_pass_arr)
			{
				if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
				$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
			}
			$pass_items.closest('tr').addClass('off').find(':input').prop("disabled", true);
		}
		else //チェックが外されたとき
		{
			data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
			for(var k in data_pass_arr)
			{
				if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない
				$show_items = $show_items.add('#'+data_pass_arr[k]);
			}
			//現在の閉じるべきもの取りなおし
			$checked = $(':checked');
			$pass_items = $();
			$checked.each(function(){
				data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
				for(var k in data_pass_arr)
				{
					if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
					$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
				}
			});
			$show_items.each(function(){
				// 閉じるべきものの中にないものだけ開く
				if($pass_items[0] && $pass_items.index($(this)) != -1 ) return;
				$show_items2 = $show_items2.add($(this));
			});
			$show_items2 = $pass_items[0] ? $show_items2 : $show_items;
			$show_items2.closest('tr').removeClass('off').find(':input').prop("disabled", false);
		}

		//テーブルの表示調整
		a11yc_table_display();
		//カウント
		a11yc_count_checkbox();
	}
	
	//table display
	function a11yc_table_display(){
		//不要な項目を隠す
		$('.a11yc form').find('.a11yc_section_guideline, .a11yc_table_check').each(function(){
			var $t = !$(this).is('table') ? $(this) : $(this).closest('.a11yc_section_criterion');
			if (!$(this).find('tr:not(.off)')[0]) //見えているものがない場合
			{
				$t.addClass('a11yc_dn');
			}
			else
			{
				$t.removeClass('a11yc_dn');
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
		num = $('.a11yc tr:visible input:not(:checked)').length;
		$info.find('thead td').text(num);
		var n = 0, subtotal = 0, total = 0; n_str = '', num_arr = [];
		//eachを表側で行う方が良いかも
		$('.a11yc_section_principle').each(function(index){
			num_arr = {'l_a':0, 'l_aa':0, 'l_aaa':0};
			$(this).find('.a11yc_section_criterion').each(function(){
				n = $(this).find('tr:visible input:not(:checked)').length;
				num_arr[$(this).data('a11ycLebel')] = num_arr[$(this).data('a11ycLebel')]+n;
			});
			$('#a11yc_rest_'+(index+1)).each(function(){
					subtotal = 0;
				$(this).find('td').each(function(index){
					n_str = '';
					if (!$(this).is('.a11yc_rest_subtotal'))
					{
						n = num_arr['l_'+$(this).data('a11ycRestLebel')];
						subtotal = subtotal+n;
						n_str = n;
						n_str = $(this).data('a11ycRestLebel').length > $current_level.length ? ' - ' : n_str;
						$(this).text(n_str);
					}
					else
					{
						$(this).text(subtotal);
					}
				});
				total = total+subtotal;
			});
		});
		$('#a11yc_rest_total').text(total);
	}
	var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
	$('#a11yc_checks :checkbox').on('click', function(){
		var select = $(this).closest('tr').find('select');
		if(c_id!=select.val()) select.val(c_id).a11yc_flash();
	});
	// チェックボックスクリック時の強調
	$('#a11yc_checks :checkbox').on('click', function(){
		$(this).closest('tr').a11yc_flash();
		if (!$('.a11yc_hide_passed_item')[0] || $(window).scrollTop()==0) return;
		var movement_distance = current_position - $(this).offset().top;
		current_position = $(this).offset().top;
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
