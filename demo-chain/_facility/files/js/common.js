
/* readyEvent
------------------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", function(e) {

	// header close btn
	$('.js-closeBtn').on('click',function(){
		$(this).parents('.js-closeBox').slideUp(400);
	});

	// fixnav
	var $nav = $('.con_header'),
		$att = $('.con_attention');
	if(!$('#fixnav').length) {
		$att.clone().attr('class','con_fixattention').appendTo('.con_fix');
		$nav.clone().attr('class','con_fixnav').appendTo('.con_fix').find('#gnav').attr('id','fixnav');
		
	}
	$w.scroll(function(){
		if($nav.offset().top < abi.sT) {
			$body.addClass('activeFix');
		}else {
			$body.removeClass('activeFix');
		}
	});
	$('.con_fixattention .js-closeBtn').on('click',function(){
		$('.con_fixattention').slideUp(400);
	});
	$('.con_attention .js-closeBtn').on('click',function(){
		$('.con_fixattention').hide();
	});
	$('.con_fixattention .js-closeBtn').on('click',function(){
		$('.con_attention').hide();
	});

	// sp menu
	var $menulist = $('#menulist');
	$('.js-btn_menu').click(function(){
		if($menulist.is('.active')) {
			$('.js-btn_menu').removeClass('active');
			$menulist.removeClass('active');
		}else {
			$('.js-btn_menu').addClass('active');
			$menulist.addClass('active');
		}
	});
	$menulist.find('.close, a, .overlay').click(function(){
		$menulist.removeClass('active');
		$('.js-btn_menu').removeClass('active');
	});
	$('.js-search-btn').click(function(){
		$('.js-btn_menu').removeClass('active');
		$menulist.removeClass('active');
	});
	$('.js-access-btn').click(function(){
		$('.js-btn_menu').removeClass('active');
		$menulist.removeClass('active');
	});

	// reserve tab
	$('.js-tabSearch').find('li').click(function(){
		var i = $(this).index();
		var $parent = $(this).parents('div');
		$(this).addClass('active').siblings().removeClass('active');
		$parent.next('.wrap_form').children('div').eq(i).fadeIn().siblings().hide();
		if(i ===  1) {
			$parent.next('.wrap_form').addClass('cancel_hide');
		}else {
			$parent.next('.wrap_form').removeClass('cancel_hide');
		}
	});
	if(abi.sp) {
		$("#contents .wrp_book .btn_book.hotel").insertAfter("#contents .js-base_Form");
		$("#contents .wrp_book .btn_book.air").insertAfter("#contents .js-air_Form");
		
		$("#modal .wrp_book .btn_book.hotel").insertAfter("#modal .js-base_Form");
		$("#modal .wrp_book .btn_book.air").insertAfter("#modal .js-air_Form");
	}

	// reserve popup
	var $modal = $('#modal');
	$('.js-search-btn').click(function(){
		// if($('#menulist').is('.active')) {
		// 	$('#menulist').removeClass('active');
		// 	$('.js-btn_menu').removeClass('active');
		// }
		// $modal.addClass('active');
		// $html.add($body).addClass('modalActive');

		if($modal.is('.active')) {
			$('.js-search-btn').removeClass('active');
			$modal.removeClass('active');
			$html.add($body).removeClass('modalActive');
		}else {
			$('.js-search-btn').addClass('active');
			$modal.addClass('active');
			$html.add($body).addClass('modalActive');
		}
	});
	$modal.find('.close,.bg_modal').click(function(){
		$('.js-search-btn').removeClass('active');
		$modal.removeClass('active');
		$html.add($body).removeClass('modalActive');
	});
	$('.js-btn_menu').click(function(){
		$('.js-search-btn').removeClass('active');
		$modal.removeClass('active');
		$html.add($body).removeClass('modalActive');
	});
	$('.js-access-btn').click(function(){
		$('.js-search-btn').removeClass('active');
		$modal.removeClass('active');
		$html.add($body).removeClass('modalActive');
	});

	// access popup
	var $modal_access = $('#modal_access');
	$('.js-access-btn').click(function(){
		if($modal_access.is('.active')) {
			$('.js-access-btn').removeClass('active');
			$modal_access.removeClass('active');
			$html.add($body).removeClass('modalaccessActive');
		}else {
			$('.js-access-btn').addClass('active');
			$modal_access.addClass('active');
			$html.add($body).addClass('modalaccessActive');
		}
	});
	$modal_access.find('.close, a').click(function(){
		$('.js-access-btn').removeClass('active');
		$modal_access.removeClass('active');
		$html.add($body).removeClass('modalaccessActive');
	});
	$('.js-btn_menu').click(function(){
		$('.js-access-btn').removeClass('active');
		$modal_access.removeClass('active');
		$html.add($body).removeClass('modalaccessActive');
	});
	$('.js-search-btn').click(function(){
		$('.js-access-btn').removeClass('active');
		$modal_access.removeClass('active');
		$html.add($body).removeClass('modalaccessActive');
	});

	//general link
	if(abi.sp) {
		$('#footer .con_links .box_txt .wrp_sns').insertAfter('#footer .con_links .box_txt .wrp_lnk ul');
	}


	$doc.click(function(){
		if($('.js-accordion').is('.active')) {
			$('.js-accordion').removeClass('active').next('ul').slideUp();
		}
	});
	$('.js-accordion').click(function(e){
		if($(this).parent().siblings().find('.st').is('.active')) {
			$(this).parent().siblings().find('.st').removeClass('active').next('ul').slideUp();
		}
		e.stopPropagation();
	});

	// smoothScroll ---------------------------//
	var speed = 1000,
		easing = 'swing',
		pcPosition = -150,
		tabPosition = -120,
		spPosition = -0;

	$('a').not('.noscroll,.js-modal').on('click', function () {
		var href = $(this).attr('href'),
			case1 = href.charAt(0) == '#',
			case2 = location.href.split('#')[0] == href.split('#')[0];
		if (case1 || case2) {
			if (case2)
				href = '#' + href.split('#')[1];

			$target = $(href);

			if ($target.length) {
				$html.add($body).not(':animated').animate({ scrollTop: String($target.offset().top + (abi.pc ? pcPosition : abi.tab ? tabPosition : spPosition)) }, speed, easing);

				return false;
			}
		}
	});

	// outerPageAnchorLink ---------------------------//
	if (window.location.href.split('#')[1] == undefined || window.location.href.split('#')[1].indexOf('=') == -1) {
		var $target = $('#' + window.location.href.split('#')[1]),
			adjust = (abi.pc) ? pcPosition : (abi.tab) ? tabPosition : spPosition;

		if ($target.length) {
			$w.on('load', function () {
				var targetPosition = $target.offset().top;
				$html.add($body).animate({ scrollTop: String(targetPosition + adjust) }, 10);
			});
		}
	}

	/* アコーディオン -----------------------------------------------------*/
	$('.accordion').on('click', function () {
		if (!$(this).is('.sp_only') || $(this).is('.sp_only') && abi.sp) {
			var $next = $(this).next();
			if (!$next.is(':animated')) $next.slideToggle(300).prev().toggleClass('active');
		}
	});

});

// auto height
function matchHeight($o,m) {
	$o.css('height','auto')
	var foo_length = $o.length;
	for(var i = 0 ; i < Math.ceil(foo_length / m) ; i++) {
		var maxHeight = 0;
		for(var j = 0; j < m; j++){
			if ($o.eq(i * m + j).height() > maxHeight) {
				maxHeight = $o.eq(i * m + j).height();
			}
		}
		for(var k = 0; k < m; k++){
			$o.eq(i * m + k).height(maxHeight);
		}
	}
}

/**
 *
 * matchHeightS
 * @param $o { jQueryObjects } - 高さを合わせるターゲット
 * @param $o { m } - 各クエリでの割合　1 で高さを auto 指定
 *
 */
function matchHeightS($o, m) {
	var _w = Math.floor((100 / m) * 10) / 10;
	var _parent = $o.parent();
	if (m > 1) {
		$o.css('height', 'auto');
		if ($o.css('float') != 'none') {
			$o.css('width', String(_w) + '%')
		} else if (_parent.css('display') == 'flex') {
			$o.css('width', String(_w) + '%');
		}
		var foo_length = $o.length;
		for(var i = 0 ; i < Math.ceil(foo_length / m) ; i++) {
			var maxHeight = 0;
			for(var j = 0; j < m; j++){
				if ($o.eq(i * m + j).height() > maxHeight) {
					maxHeight = $o.eq(i * m + j).height();
				}
			}
			for(var k = 0; k < m; k++){
				$o.eq(i * m + k).height(maxHeight);
			}
		}
	} else {
		$o.css('height', 'auto');
		if ($o.css('float') != 'none') {
			$o.css('width', String(_w) + '%')
		} else if (_parent.css('display') == 'flex') {
			$o.css('width', String(_w) + '%');
		}
	}
}