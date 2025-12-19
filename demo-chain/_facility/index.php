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
<link href="<?php echo echo_version(LOCATION_FACILITY_FILE . 'css/homepage.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
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
					<?php
					if (isset($view)) {
						echo $view->partial('_Facility/Top/main_image', ['cms__top_page_setting' => $cms__top_page_setting]);
					}
					?>
				</div>
			</div>

			<div class="p-wrp_breadcrumb">
				<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
					<?php include LOCATION_ROOT_DIR . "/templates/breadcrumbs.php"; ?>
				</ul><!-- /.topicpath -->
			</div>

			<div class="wrp_pickup">
				<?php
				if (isset($view)) {
					echo $view->partial('Banner/list', ['cms__banner_list' => $cms__top_banner_list]);
				}
				?>
			</div>

			<div class="wrp_news">
				<div class="con_news p-news">
					<h2 class="st c-st1">
						<i>NEWS</i>
						<span>お知らせ</span>
					</h2>
					<div class="box_news">
						<?php
						if (isset($view)) {
							echo $view->partial('Common/news_list', ['cms__news_list' => $cms__news_list, 'base_url' => LOCATION_FACILITY]);
						}
						?>
					</div>
					<p class="btn c-btn1"><a href="<?php echo LOCATION_FACILITY; ?>news/">一覧を見る</a></p>
				</div>
			</div>


			<section class="con_info">
				<h2 class="st c-st1">
					<i>HOTEL INFORMATION</i>
					<span>ホテル情報</span>
				</h2>
				<?php
				if (isset($view)) {
					echo $view->partial('_Facility/Top/hotel_info', [
						'cms__top_page_setting' => $cms__top_page_setting,
						'cms__facility_setting' => $cms__facility_setting
					]);
				}
				?>

				<ul class="box_btn">
					<li class="c-btn1"><a href="<?php echo LOCATION_FACILITY; ?>access/">アクセスの詳細を見る</a></li>
				</ul>
			</section>

			<?php include LOCATION_ROOT_DIR . "../../templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/css/modaal.min.css" integrity="sha384-fCp5T7Jho7XA7FhIWeqR9vj8ZxGoporWYpDGXc3XQPU93zEKdK8cESf1l3YO/lRk" crossorigin="anonymous">
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" integrity="sha384-YGnnOBKslPJVs35GG0TtAZ4uO7BHpHlqJhs0XK3k6cuVb6EBtl+8xcvIIOKV5wB+" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/js/modaal.min.js" integrity="sha384-N1NyMYVZ/NtKw105M5oKbEbqMy33NawGjCQKMdPC4aH/CbW0NR0Z9hZ5F1AmOkIC" crossorigin="anonymous"></script>
	<script src="<?php echo echo_version(LOCATION_BASE . '_facility/files/js/homepage.min.js', LOCATION_FILE_DIR); ?>"></script>
	<!-- #abi_page -->
</body>

</html>