<?php
$fancy_views = [];
$fancy_views_cnt = 0;

$page = 'rooms';
$pageName = $cms__page_setting['title'] ?? '客室';
include realpath(__DIR__ . '/../config/include.php');
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<!-- *** stylesheet *** -->
<link href="<?php echo echo_version(LOCATION_FACILITY_FILE . 'css/' . $page . '.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
<script>
	document.addEventListener("DOMContentLoaded", function(e) {
		$('[data-fancybox]').fancybox({
			mobile: {
				clickContent: function(current, event) {
					return current.type === 'image' ? 'toggleControls' : false;
				},
				clickSlide: function(current, event) {
					return current.type === 'image' ? 'close' : 'close';
				},
				dblclickContent: function(current, event) {
					return current.type === 'image' ? 'zoom' : false;
				},
				dblclickSlide: function(current, event) {
					return current.type === 'image' ? 'zoom' : false;
				}
			},
		});
		if (abi.sp) {
			$('.con_int').each(function() {
				var $int_txt = $(this).find('.box_txt .txt'),
					$int_photo = $(this).find('.photo');
				$int_txt.after($int_photo);
			});
			$('.con_rooms .box_rooms .box_detail').each(function() {
				var $rooms_pop = $(this).find('.wrp_detail table'),
					$rooms_txt = $(this).find('.romms_pop');
				$rooms_pop.after($rooms_txt);
			});
		} else {
			$('.con_int').each(function() {
				var $int_txt = $(this).find('.box_txt'),
					$int_photo = $(this).find('.photo');
				$int_txt.after($int_photo);
			});
			$('.con_rooms .box_rooms .box_detail').each(function() {
				var $rooms_pop = $(this).find('.wrp_detail'),
					$rooms_txt = $(this).find('.romms_pop');
				$rooms_pop.after($rooms_txt);
			});
		}

		$match_info = $('.con_rooms .box_rooms .box_rooms_info .box_txt').find('.txt');
		$match_info2 = $('.con_rooms .box_rooms .box_rooms_info .box_txt').find('.st');
		matchHeight($match_info, (abi.pc) ? 3 : (abi.tab) ? 3 : 1);
		matchHeight($match_info2, (abi.pc) ? 3 : (abi.tab) ? 3 : 1);

		//各客室　スライダー
		$('.box_slider .box_img').slick({
			fade: true,
			arrows: true,
			autoplaySpeed: 2500,
			speed: 600,
			dots: true,
			autoplay: false,
			pauseOnHover: false,
			pauseOnFocus: false,
			prevArrow: '<p class="prev"><i class="ic-chevron-thin-left"></i></p>',
			nextArrow: '<p class="next"><i class="ic-chevron-thin-right"></i></p>',
			customPaging: function(slick, index) {
				return '';
			}
		});

		$('.facility_karasuma #link_r1 .rooms_ame .st').addClass('active');
		$('#link_r2 .rooms_ame .st').addClass('active');
		$('#link_r3 .rooms_ame .st').addClass('active');
		$('#link_r4 .rooms_ame .st').addClass('active');
		$('#link_r5 .rooms_ame .st').addClass('active');
		$('.facility_karasumagojo #link_r6 .rooms_ame .st').addClass('active');
	});
</script>
</head>

<body id="<?php echo $page; ?>">
	<?php include LOCATION_ROOT_DIR . "/templates/gtm.php"; ?>
	<div id="abi_page">
		<?php include_once LOCATION_ROOT_DIR . '/files/images/common/symbol-defs.svg'; ?>
		<?php include LOCATION_ROOT_DIR . "/templates/header.php"; ?>
		<main id="contents">
			<?php if (isset($view)) {
				echo $view->partial('_Facility/Common/main_image', ['cms__page_setting' => $cms__page_setting, 'pageName' => $pageName]);
			} ?>
			<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
				<?php include LOCATION_ROOT_DIR . "/templates/breadcrumbs.php"; ?>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?php echo $pageName; ?></span>
					<meta property="position" content="3">
				</li>
			</ul><!-- /.topicpath -->

			<?php if (isset($view)) {
				echo $view->partial('_Facility/Rooms/intro', ['cms__rooms_info' => $cms__rooms_info]);
				echo $view->partial('_Facility/Rooms/list', ['cms__rooms_list' => $cms__rooms_list]);
			} ?>

			<?php include LOCATION_ROOT_DIR . "../../templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>
	<!-- #abi_page -->
	<script src="https://rawcdn.githack.com/MozillaReality/immersive-custom-elements/v0.1.0/build/immersive-custom-elements.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.0/slick/slick.min.css" integrity="sha384-rvlZgyB/e6/KpJ0QZStyw4b8+iJdD0J59B9GlYwU5G8eNGdeoKjSaCQ0zHJqwFvn" crossorigin="anonymous">
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" integrity="sha384-YGnnOBKslPJVs35GG0TtAZ4uO7BHpHlqJhs0XK3k6cuVb6EBtl+8xcvIIOKV5wB+" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" integrity="sha384-Q8BgkilbsFGYNNiDqJm69hvDS7NCJWOodvfK/cwTyQD4VQA0qKzuPpvqNER1UC0F" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js" integrity="sha384-Zm+UU4tdcfAm29vg+MTbfu//q5B/lInMbMCr4T8c9rQFyOv6PlfQYpB5wItcXWe7" crossorigin="anonymous"></script>
</body>

</html>