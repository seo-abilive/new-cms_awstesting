$(function(){

	// ▼▼▼ ダイレクトイン 検索窓　▼▼▼--------------------------------------------

	//オブジェクトの設定
	var $datepicker = $('.datepicker');
	var $selectModal = $('#js-select_hotel');
	var $formSearch = $('.js-base_Form');
	var $partsRsvId = $('.parts_id');
	var $partsDir = $('.parts_dir');
	var $partsEdg = $('.parts_edg');
	var $partsBkpro = $('.parts_bkpro');
	var $nodate = $('.js-nodate');
	
	// ホテル選択
	$('.js-selectHotel').change(function(){
		$this = $(this).find('option:selected');
		i = $this.index();
		var rsvSystem = $this.attr('data-rsv');
		var rsvId = $this.data('id');
		var rsvSp = $this.data('mobile-page');
		
		$('.box_nod').find('input').prop('checked',false);
		if(!rsvId == '') {
			$formSearch.find('.disabled').removeClass('disabled').find('input,select,label').prop('disabled', false);
			$('.js-selectHotel').each(function(){
				$(this).find('option').eq(i).prop('selected',true).siblings('.default').remove();
			});
		}
		
		if(rsvSystem === 'dir:ver3m') {
			$formSearch.attr('action','https://asp.hotel-story.ne.jp/ver3m/planlist.asp');
			$formSearch.attr('method','get');
			$partsRsvId.attr('value',rsvId);
			$partsEdg.prop('disabled', true);
			$partsBkpro.prop('disabled', true);
			$formSearch.find('.box_roo,.box_chg').removeClass('hide');
			if(rsvSp === 0) {
				$partsDir.prop('disabled', false);
				$('.js-submit_dir').hide().siblings().show();
			}else {				
				if(abi.sp) {
					$('.js-submit_dir').show().siblings().hide();
				}else {
					$partsDir.prop('disabled', false);
				}
			}
		}else if(rsvSystem === 'dir' || rsvSystem === 'dir:ver3d') {
			$formSearch.attr('action','https://asp.hotel-story.ne.jp/ver3d/planlist.asp');
			$formSearch.attr('method','get');
			$partsRsvId.attr('value',rsvId);
			$partsEdg.prop('disabled', true);
			$partsBkpro.prop('disabled', true);
			$formSearch.find('.box_roo,.box_chg').removeClass('hide');
			if(rsvSp === 0) {
				$partsDir.prop('disabled', false);
				$('.js-submit_dir').hide().siblings().show();
			}else {				
				if(abi.sp) {
					$('.js-submit_dir').show().siblings().hide();
				}else {
					$partsDir.prop('disabled', false);
				}
			}
		}else if(rsvSystem === 'edg') {
			$formSearch.attr('action','https://redirect.fastbooking.com/DIRECTORY/dispoprice.phtml');
			$formSearch.attr('method','get');
			$partsRsvId.attr('value',rsvId);
			$formSearch.find('.box_roo,.box_chg').addClass('hide');
			$partsDir.prop('disabled', true);
			$partsEdg.prop('disabled', false);
			$partsBkpro.prop('disabled', true);
			$('.js-submit_dir').hide().siblings().show();
		}else if(rsvSystem === 'bkpro') {
			var bkpro_action = 'https://www5.489pro.com/asp/489/menu.asp?';
			bkpro_action += 'id=';
			bkpro_action += rsvId;
			bkpro_action += '&ty=ser&list=YES&liop=1&lan=JPN';
			$formSearch.attr('action',bkpro_action);
			$formSearch.attr('method','post');
			$partsDir.prop('disabled', true);
			$partsEdg.prop('disabled', true);
			$partsBkpro.prop('disabled', false);
			$formSearch.find('.box_roo,.box_chg').removeClass('hide');
			$('.js-submit_dir').hide().siblings().show();
		}
	});

	// select change
	$('.parts').find('select').change(function(){
		var value = $(this).val();
		if($(this).hasClass('js-per')) {
			$('.parts_per').attr('value',value);
		}else if($(this).hasClass('js-roo')) {
			$('.parts_roo').attr('value',value);	

		}else if($(this).hasClass('js-sta')) {
			$('.parts_sta').attr('value',value);
			
			var checkinDate = $(this).parents('form').find('.datepicker').val();
			if(!(checkinDate === '')) {
				var date = new Date(checkinDate);
				date.setDate(date.getDate() + Number($(this).val()));
				var y = date.getFullYear();
				var m = date.getMonth() + 1;
				var d = date.getDate();
				$('.parts_dat2_y').val(y);
				$('.parts_dat2_m').val(m);
				$('.parts_dat2_d').val(d);
			}
		}
	});

	// 日付未定
	$('.js-nodate').change(function(){
		if($(this).prop('checked')) {
			$('.parts_dat').prop('disabled', true);
			$datepicker.prop('disabled', true).parent().addClass('disabled');
		} else {
			$('.parts_dat').prop('disabled', false);
			$datepicker.prop('disabled', false).parent().removeClass('disabled');
		}			
	});

	// ホテル未選択時アラート
	$('.cover').click(function(){
		var hotelVal = $(this).closest('form').find('.parts_id').val();
		if(hotelVal == '') {
			// alert(select_hotel);
			alert('ホテルを選択してください');
		}
	});

	//datepicker
	$datepicker.datepicker({
		monthNames : ['1','2','3','4','5','6','7','8','9','10','11','12'],
		monthNamesShort : ['1','2','3','4','5','6','7','8','9','10','11','12'],
		dayNames : ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
		dayNamesMin : ['日','月','火','水','木','金','土'],
		yearSuffix : '年',
		dateFormat : 'yy/mm/dd',
		nextText : '<i class="ic-chevron-right"></i>', 
		prevText : '<i class="ic-chevron-left"></i>',
		showOtherMonths : true,
		selectOtherMonths : true,
		firstDay : 0,
		isRTL : false,
		showMonthAfterYear : true,
		numberOfMonths : 2,
		minDate : '-1d',
		maxDate : '+364d',
		onSelect : function(dateText, inst) {
			var stay = Number($(this).parents('form').find('.js-sta').val());
			var arrDate = dateText.split('/');
			var date = new Date(dateText);
			date.setDate(date.getDate() + stay);
			var y = date.getFullYear();
			var m = date.getMonth() + 1;
			var d = date.getDate();

			$(this).val(dateText);
			$('.parts_dat').val(dateText);
			$('.parts_dat1_y').val(arrDate[0]);
			$('.parts_dat1_m').val(arrDate[1]);
			$('.parts_dat1_d').val(arrDate[2]);
			$('.parts_dat2_y').val(y);
			$('.parts_dat2_m').val(m);
			$('.parts_dat2_d').val(d);

			$('.parts_bkpro_y').val(arrDate[0]);
			$('.parts_bkpro_m').val(arrDate[1]);
			$('.parts_bkpro_d').val(arrDate[2]);
		}
	});

	// SP時転送処理
	$formSearch.find('.js-submit_sp').click(function(){
		var $parent = $(this).closest('form');
		aspURL = 'https://asp.hotel-story.ne.jp/ktai/ZKETAI0050.asp';
		var hcod1 = $parent.find('.parts_id').val(),
		hcod2 = '001';
		
		var urlParam = '';
		urlParam += '?cod1=' + hcod1;
		urlParam += '&cod2=' + hcod2;
		urlParam += '&reffrom=ZKETAI0010';
		
		urlParam += '&adult=' + $parent.find('.parts_per').val();
		urlParam += '&child=' + '0';
		urlParam += '&child=' + '0';
		urlParam += '&child=' + '0';
		urlParam += '&child=' + '0';
		
		var arrDate = new String($parent.find('.parts_dat').val()).split('/');
		
		urlParam += '&arrm=' + arrDate[1];
		urlParam += '&arrd=' + arrDate[2];
		urlParam += '&haks=' + $parent.find('.parts_sta').val();
		urlParam += '&rooms=' + $parent.find('.parts_roo').val();
		
		window.open(decorateForm(aspURL + urlParam));
		return false;
	});


	// ▼▼▼ 航空券付き検索窓 ▼▼▼--------------------------------------------
	var $formAir = $('.js-air_Form');

	$('.datepicker_air').datepicker({
		monthNames : ['1','2','3','4','5','6','7','8','9','10','11','12'],
		monthNamesShort : ['1','2','3','4','5','6','7','8','9','10','11','12'],
		dayNames : ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
		dayNamesMin : ['日','月','火','水','木','金','土'],
		dateFormat : 'yy/mm/dd',
		nextText : '<i class="ic-chevron-right"></i>', 
		prevText : '<i class="ic-chevron-left"></i>',
		showOtherMonths : true,
		selectOtherMonths : true,
		firstDay : 0,
		isRTL : false,
		showMonthAfterYear : true,
		yearSuffix : '年',
		numberOfMonths : 2,
		minDate : '+0d'
	});

	$('.js-area').change(function(){
		var areaName = $(this).val();
		var $parentsForm = $(this).parents('form');
		if(areaName === '') {
			$parentsForm.find('.js-hotel').find('optgroup').show().find('option').prop('selected',false);
		}else {
			$parentsForm.find('.js-hotel').find('optgroup[label="'+ areaName + '"]').show().find('option').first().prop('selected',true).parent().siblings().hide();
			var $activeOption = $('optgroup[label="'+ areaName + '"] option').first();
			var hotelName = $activeOption.val();
			$parentsForm.find('.js-air_hotel1').attr('value',hotelName);
			$parentsForm.find('.js-air_hotel2').attr('value','builtingadget_'+hotelName);
			$formAir.find('.box_sub .parts').removeClass('disabled');
		}
	});
	$('.js-hotel').change(function(){
		var hotelName = $(this).val();
		var $parentsForm = $(this).parents('form');		
		if(hotelName === '') {
		}else {
			$parentsForm.find('.js-air_hotel1').attr('value',hotelName);
			$parentsForm.find('.js-air_hotel2').attr('value','builtingadget_'+hotelName);
			$formAir.find('.box_sub .parts').removeClass('disabled');
		}
	});

	// ホテル未選択時アラート
	$formAir.find('.js-submit').click(function(){
		var hotelVal = $(this).closest('form').find('.js-hotel').val();
		if(hotelVal == '') {
			// alert(select_hotel);
			alert('ホテルを選択してください');
			return false;
		}
	});		

	// ▼▼▼ 共通 ▼▼▼--------------------------------------------

	// 多言語処理
	if ($html.hasClass('lang_en')) {
		$('.hasDatepicker').datepicker("option", { 
			monthNames : ['January','February','March','April','May','June','July','August','September','October','November','December'],
			monthNamesShort : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
			dayNames : ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
			dayNamesMin : ['Su','Mo','Tu','We','Th','Fr','Sa'],
			yearSuffix : ''
		});
	} else if ($html.hasClass('lang_cn')) {
		$('.hasDatepicker').datepicker("option", { 
			monthNames : ['1','2','3','4','5','6','7','8','9','10','11','12'],
			monthNamesShort : ['1','2','3','4','5','6','7','8','9','10','11','12'],
			dayNames : ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
			dayNamesMin : ['日','一','二','三','四','五','六'],
			yearSuffix : ''
		});
	} else if ($html.hasClass('lang_tw')) {
		$('.hasDatepicker').datepicker("option", { 
			monthNames : ['一','二','三','四','五','六','七','八','九','十','十一','十二'],
			monthNamesShort : ['一','二','三','四','五','六','七','八','九','十','十一','十二'],
			dayNames : ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
			dayNamesMin : ['日','一','二','三','四','五','六'],
			yearSuffix : ''
		});
	} else if ($html.hasClass('lang_ko')) {
		$('.hasDatepicker').datepicker("option", { 
			monthNames : ['1','2','3','4','5','6','7','8','9','10','11','12'],
			monthNamesShort : ['1','2','3','4','5','6','7','8','9','10','11','12'],
			dayNames : ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
			dayNamesMin : ['일','월','화','수','목','五','六'],
			yearSuffix : ''
		});
	}
	
	$w.superResize({
		//resize
		resizeAfter : function(){
			if(abi.sp) {
				$datepicker.datepicker("option", "numberOfMonths", 1);
				$('.datepicker_air').datepicker("option", "numberOfMonths", 1);
				$formSearch.find('.js-submit').prop('disabled', true);
			}else {
				$datepicker.datepicker("option", "numberOfMonths", 2);
				$('.datepicker_air').datepicker("option", "numberOfMonths", 2);
			}
		}
	});

	$('#modal .hasDatepicker').click(function(){
		if(abi.sp) {
			var height = $(this).height();
			var scroll = $w.scrollTop();
			var offset = $(this).offset().top;
			var calH = $('#ui-datepicker-div').outerHeight();
			var position = offset - scroll - calH;
			$('#ui-datepicker-div').css('top',position+'px');
		}
	});

});

//クロスドメイン SP用
function decorateForm(url){
    var gobj = window[window.GoogleAnalyticsObject];
    if(typeof gobj !== 'undefined') {
        tracker = gobj.getAll()[0];
        var linker = new window.gaplugins.Linker(tracker);
         return linker.decorate(url);
    }else{
        return url;
    }
}
/*add_クロスドメイン対応↑*/

//検索
function submitForm(el) {
	decorateForm(target);
	$(el).submit();
	return false;
}
