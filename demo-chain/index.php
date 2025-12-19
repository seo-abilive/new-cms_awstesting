<?php
$page = 'homepage';
include realpath(__DIR__ . '/config/include.php');
?>
<?php if (file_exists(LOCATION_ROOT_DIR . '/mobile_thumbnail.jpg') && $modern) : ?>
	<?php
	// モバイルサムネールの画像サイズ
	$mobile_thumb_width = 100;
	$mobile_thumb_height = 130;
	?>
	<PageMap>
		<DataObject type="thumbnail">
			<Attribute name="src" value="<?php echo LOCATION . 'mobile_thumbnail.jpg'; ?>" />
			<Attribute name="width" value="<?php echo $mobile_thumb_width; ?>" />
			<Attribute name="height" value="<?php echo $mobile_thumb_height; ?>" />
		</DataObject>
	</PageMap>
	<meta name="thumbnail" content="<?php echo LOCATION . 'mobile_thumbnail.jpg'; ?>" />
<?php endif; ?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<link href="<?php echo echo_version(LOCATION_FILE . 'css/homepage.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
</head>

<body id="<?php echo $page; ?>">
	<?php include LOCATION_ROOT_DIR . "/templates/gtm.php"; ?>
	<div id="abi_page">
		<?php include_once LOCATION_ROOT_DIR . '/files/images/common/symbol-defs.svg'; ?>
		<?php include LOCATION_ROOT_DIR . "/templates/header.php"; ?>
		<main id="contents">

			<div class="con_mainimg">
				<div class="box_img" id="js-mainimg">
					<div class="slide"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_519e3e65deb55a498869db22e7ea4547e0185bb5.jpg" data-pc="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_519e3e65deb55a498869db22e7ea4547e0185bb5.jpg" data-sp="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/960x960_ea1e9d427fb5664c32c517a73e421e58_11e387a7710156211703f1157ba586008459fd48.jpg" alt="demo"></div>
					<div class="slide"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_13f7d91904b624ac9c832247331e54fefb516555.jpg" data-pc="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_13f7d91904b624ac9c832247331e54fefb516555.jpg" data-sp="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/960x960_ea1e9d427fb5664c32c517a73e421e58_5b69d705bdf1037733c804987b7d525c381e7809.jpg" alt="demo"></div>
					<div class="slide"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_0d095d3a1d803ea87f979bf03287d2bdcae6f985.jpg" data-pc="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/1920x661_ea1e9d427fb5664c32c517a73e421e58_0d095d3a1d803ea87f979bf03287d2bdcae6f985.jpg" data-sp="https://demo.abi-cms.net//hotel_full/datas/cache/images/2023/01/05/960x960_ea1e9d427fb5664c32c517a73e421e58_f4c4f78b1d6d11ff1b5687d1d9b33200b346c108.jpg" alt="demo"></div>
				</div>
				<div class="c-multilingual">
					<?php include LOCATION_ROOT_DIR . "/templates/multilingual_list.php"; ?>
				</div>
			</div>

			<?php if (isset($view)) {
				echo $view->partial('Banner/list', ['cms__banner_list' => $cms__banner_list]);
			} ?>

			<div id="modal_top_banner111" class="modal_disp">
				<div class="movie">
					<iframe width="560" height="315" src="#123" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>

			<div id="modal_top_banner121" class="modal_disp">
				<div class="movie">
					<iframe width="560" height="315" src="https://www.tokyo-skytree.jp/" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>

			<div class="wrp_news">
				<section class="con_news p-news">
					<h2 class="st c-st1">
						<i>NEWS</i>
						<span>お知らせ</span>
					</h2>
					<?php if (isset($view)) {
						echo $view->partial('Common/news_list', ['cms__news_list' => $cms__news_list]);
					} ?>
					<p class="btn c-btn1"><a href="<?php echo LOCATION; ?>news/">一覧を見る</a></p>
				</section>
			</div>

			<?php include LOCATION_ROOT_DIR . "/templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/css/modaal.min.css" integrity="sha384-fCp5T7Jho7XA7FhIWeqR9vj8ZxGoporWYpDGXc3XQPU93zEKdK8cESf1l3YO/lRk" crossorigin="anonymous">
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" integrity="sha384-YGnnOBKslPJVs35GG0TtAZ4uO7BHpHlqJhs0XK3k6cuVb6EBtl+8xcvIIOKV5wB+" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/js/modaal.min.js" integrity="sha384-N1NyMYVZ/NtKw105M5oKbEbqMy33NawGjCQKMdPC4aH/CbW0NR0Z9hZ5F1AmOkIC" crossorigin="anonymous"></script>
	<script src="<?php echo echo_version(LOCATION_FILE . 'js/homepage.min.js', LOCATION_FILE_DIR); ?>"></script>
	<!-- #abi_page -->
</body>

</html>