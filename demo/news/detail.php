<?php
$page = 'news';

include realpath(__DIR__ . '/../config/include.php');

$title = $cms__news['title'] ?? '';
$category = $cms__news['category']['title'] ?? '';
$publicDate = $cms__news['public_date'] ?? '';
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<!-- *** stylesheet *** -->
<link href="<?php echo echo_version(LOCATION_FILE . 'css/' . $page . '.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
</head>

<body id="<?php echo $page; ?>">
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v17.0&appId=384634828262323&autoLogAppEvents=1" nonce="2am7gldY"></script>
	<?php include LOCATION_ROOT_DIR . "/templates/gtm.php"; ?>
	<div id="abi_page">
		<?php include LOCATION_ROOT_DIR . "/templates/header.php"; ?>
		<main id="contents">
			<!-- パンくず -->
			<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION; ?>" typeof="WebPage">
						<span property="name">Home</span>
					</a>
					<meta property="position" content="1">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION; ?>news/" typeof="WebPage">
						<span property="name">新着情報</span>
					</a>
					<meta property="position" content="2">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?php echo $title; ?></span>
					<meta property="position" content="3">
				</li>
			</ul>
			<div class="wrap_news">


				<div class="con_news">
					<?php
					if (isset($view)) {
						echo $view->partial('News/detail', ['news' => $cms__news, 'sibLings' => $cms__sibLings]);
					}
					?>

				</div>


				<?php include LOCATION_ROOT_DIR . "/templates/news_side.php"; ?>
			</div>
		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>
	<!-- #abi_page -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" integrity="sha384-YGnnOBKslPJVs35GG0TtAZ4uO7BHpHlqJhs0XK3k6cuVb6EBtl+8xcvIIOKV5wB+" crossorigin="anonymous"></script>
	<script src="<?php echo echo_version(LOCATION_FILE . 'js/news.min.js', LOCATION_FILE_DIR); ?>"></script>
	<?php
	if (isset($view)) {
		echo $view->partial('Common/markup', ['cms__markup_data' => $cms__markup_data]);
	}
	?>
</body>

</html>