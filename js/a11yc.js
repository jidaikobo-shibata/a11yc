jQuery(function($){

// checklists, bulk
if($('form #a11yc_checks')[0])
{

	// レベルを絞り込み
	a11yc_nallow_level();
	$('#a11yc_narrow_level a').on('click', a11yc_nallow_level);
	
	function a11yc_nallow_level(e){
		var $target = e ? $(e.target) : $('#a11yc_narrow_level .current');
		var data_levels = $target.data('narrowLevel') ? $target.data('narrowLevel').split(',') : [];
		var $show_levels = $();
		$target.parent().find('a').removeClass('current');
		$target.addClass('current');
		for (var k in data_levels)
		{
			$show_levels = $show_levels.add($('.'+data_levels[k]));
		}
		$('.section_criterion').hide();
		$show_levels.show();
		
		//テーブルの表示切り替え
		a11yc_toggle_table();
	}

	//チェック項目の表示・非表示切り替え
	var $info = $(),
			num = 0,
			interval = 0,
			$checked = $(),
			data_pass_arr = [],
			$pass_items = $(),
			$show_items = $(),
			$show_items2 = $();
	
	$info = $('#a11yc_info');

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
			$pass_items.closest('tr').addClass('off');
		}
		else if(input.prop('checked')) // チェックされたとき
		{
			data_pass_arr = $(this).data('pass') ? $(this).data('pass').split(',') : [];
			for(var k in data_pass_arr)
			{
				if(data_pass_arr[k]==this.id) continue; //自分自身は相手にしない？
				$pass_items = $pass_items.add('#'+data_pass_arr[k]); 
			}
			$pass_items.closest('tr').addClass('off');
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
			$show_items2.closest('tr').removeClass('off');
		}
	
		//カウント
		interval = !input ? 0 : 500;
		setTimeout(function(){
			num = $(':checkbox:not(:checked):visible').length;
			$info.find('span').text(num);
		}, interval);
		
		//テーブルを隠すかどうか
		a11yc_toggle_table();
	}
	
	//tableを隠す
	function a11yc_toggle_table(){
		$('#a11yc_checks').find('table').each(function(){
			if(!$(this).find('tr:not(.off)')[0])
			{
				console.log('hide: '+$(this).closest('.section_criterion')[0].id);
				$(this).closest('.section_criterion').hide();
			}
			else
			{
				$(this).closest('.section_criterion').show();
			}
		});
	}


	// チェックした人の反映
	var c_id = $('#a11yc_checks').data('a11ycCurrentUser');
	$(':checkbox').on('click', function(){
		var select = $(this).closest('tr').find('select');
		if(c_id!=select.val()) select.val(c_id).a11yc_flash();
	});
	$(':checkbox').on('click', function(){
		$(this).closest('tr').a11yc_flash();
	});

}


/* === bulk === */
// a11yc_update_done
$('#a11yc_update_done').parent().hide();
$('#a11yc_update_all').on('change', function(){
	if($(this).val() > 1)
	{
		$('#a11yc_update_done').parent().show();
	}
	else
	{
		$('#a11yc_update_done').parent().hide();
	}
});

/* === docs === */
var $a11yc_docs_c = $('#a11yc_docs .section_criterions');
if($a11yc_docs_c[0])
{
	$a11yc_docs_c.addClass('a11yc_disclosure_target').hide();
	$a11yc_docs_c.find('ul').addClass('a11yc_disclosure_target').hide();
	$('#a11yc_docs').find('h3, h4').addClass('a11yc_disclosure').attr('tabindex', 0);
}

/* disclosure */
$(document).on('click', '.a11yc_disclosure', function(){
	var index = $(this).index('.a11yc_disclosure');
	$(this).toggleClass('on');
	$(document).find('.a11yc_disclosure_target').eq(index).slideToggle(250);
});

/* === 動作補助 === */

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
