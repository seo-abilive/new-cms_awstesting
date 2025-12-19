<?php
//タイトル
$news_title = $cms__news['title'] ?? '';
$category = $cms__news['category']['title'] ?? '';
$publicDate = $cms__news['public_date'] ?? '';
?>
<?php
$page = 'news_detail';
$pageName = 'お知らせ';
include realpath(__DIR__ . '/../config/include.php');
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<link href="<?php echo echo_version(LOCATION_FILE . 'css/news.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
</head>

<body id="<?php echo $page; ?>">
	<div id="fb-root"></div>
	<?php include LOCATION_ROOT_DIR . "/templates/gtm.php"; ?>
	<div id="abi_page">
		<?php include_once LOCATION_ROOT_DIR . '/files/images/common/symbol-defs.svg'; ?>
		<?php include LOCATION_ROOT_DIR . "/templates/header.php"; ?>
		<main id="contents">
			<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION_GENERAL; ?>" typeof="WebPage">
						<span property="name">HOME</span>
					</a>
					<meta property="position" content="1">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION . 'news/'; ?>" typeof="WebPage">
						<span property="name"><?php echo $pageName; ?></span>
					</a>
					<meta property="position" content="3">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?php echo $news_title; ?></span>
					<meta property="position" content="4">
				</li>
			</ul><!-- /.topicpath -->

			<div class="wrap_news">
				<div class="con_news">
					<?php if (isset($view)) {
						echo $view->partial('News/detail', ['news' => $cms__news, 'sibLings' => $cms__sibLings]);
					} ?>
				</div>
				<?php include LOCATION_ROOT_DIR . "/templates/news_side.php"; ?>
			</div>

			<?php include LOCATION_ROOT_DIR . "/templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/css/modaal.min.css" integrity="sha384-fCp5T7Jho7XA7FhIWeqR9vj8ZxGoporWYpDGXc3XQPU93zEKdK8cESf1l3YO/lRk" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/js/modaal.min.js" integrity="sha384-N1NyMYVZ/NtKw105M5oKbEbqMy33NawGjCQKMdPC4aH/CbW0NR0Z9hZ5F1AmOkIC" crossorigin="anonymous"></script>
	<!-- #abi_page -->
	<?php if (isset($view)) {
		echo $view->partial('Common/markup', ['cms__markup_data' => $cms__markup_data]);
	} ?>
</body>

</html>