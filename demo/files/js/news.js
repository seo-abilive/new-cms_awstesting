$(function () {
	$('#js-newsSlider').slick({
		fade: false,
		infinite: true,
		autoplaySpeed: 3500,
		speed: 1000,
		autoplay: true,
		arrows: true,
		dots: true,
		slidesToShow: 1,
		pauseOnFocus: false,
		pauseOnHover: false,
		dotsClass: 'slick-dots c-slick_dots',
		prevArrow: '<button class="slick-prev slick-arrow c-slick_arrows" aria-label="Prev" type="button" style="">Prev</button>',
		nextArrow: '<button class="slick-next slick-arrow c-slick_arrows" aria-label="Next" type="button" style="">Next</button>',
	});
});