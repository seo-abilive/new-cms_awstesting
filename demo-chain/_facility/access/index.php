<?php
$page = 'access';
$pageName = $cms__page_setting['title'] ?? 'アクセス';
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
		$match_info = $('.con_route .box_route .wrp_route ul.box_route_info').find('li');
		// if(!abi.sp) {
		// 	matchHeight($match_info, 2);
		// }
		$match_info_sst = $('.con_route .box_route .wrp_route ul.box_route_info li').find('.info_sst');
		$match_info_txt = $('.con_route .box_route .wrp_route ul.box_route_info li').find('.inner');
		$w.on('load', function() {
			if (!abi.sp) {
				matchHeight($match_info_sst, 2);
				matchHeight($match_info_txt, 2);
			}
		});

		$('.con_route .box_route .wrp_route .btn_open').on('click', function() {
			$('.con_route .box_route .wrp_route .box_route_map, .con_route .box_route .wrp_route .box_route_info, .con_route .box_route .wrp_route .btn_close').fadeIn();
			$('.con_route .box_route .wrp_route .btn_open').css('opacity', '0');
			if (!abi.sp) {
				matchHeight($match_info, 2);
			}
		});
		$('.con_route .box_route .wrp_route .btn_close').on('click', function() {
			$('.con_route .box_route .wrp_route .box_route_map, .con_route .box_route .wrp_route .box_route_info, .con_route .box_route .wrp_route .btn_close').fadeOut();
			$('.con_route .box_route .wrp_route .btn_open').css('opacity', '1');
		});
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
				echo $view->partial('_Facility/Access/access', [
					'cms__access' => $cms__access,
					'cms__facility_setting' => $cms__facility_setting
				]);
			} ?>

			<?php include LOCATION_ROOT_DIR . "../../templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>
	<!-- #abi_page -->

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" integrity="sha384-Q8BgkilbsFGYNNiDqJm69hvDS7NCJWOodvfK/cwTyQD4VQA0qKzuPpvqNER1UC0F" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js" integrity="sha384-Zm+UU4tdcfAm29vg+MTbfu//q5B/lInMbMCr4T8c9rQFyOv6PlfQYpB5wItcXWe7" crossorigin="anonymous"></script>
	<script>
		$(document).ready(function() {
			$('[data-fancybox]').fancybox({
				mobile: {
					clickContent: function(current, event) {
						return current.type === 'image' ? 'toggleControls' : false;
					},
					clickSlide: function(current, event) {
						return current.type === 'image' ? 'close' : false;
					},
					dblclickContent: function(current, event) {
						return current.type === 'image' ? 'zoom' : false;
					},
					dblclickSlide: function(current, event) {
						return current.type === 'image' ? 'zoom' : false;
					}
				},
			});
		});
	</script>
</body>

</html>