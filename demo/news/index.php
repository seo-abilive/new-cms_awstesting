<?php 
$page = 'news';
$pageName = 'お知らせ';
include realpath(__DIR__.'/../config/include.php');
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR."/templates/common_css.php"; ?>
<!-- *** stylesheet *** -->
<link href="<?php echo echo_version(LOCATION_FILE.'css/'. $page .'.css',LOCATION_FILE_DIR);?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR."/templates/common_js.php"; ?>
</head>

<body id="<?php echo $page; ?>">
<?php include LOCATION_ROOT_DIR."/templates/gtm.php"; ?>
<div id="abi_page">
<?php include LOCATION_ROOT_DIR."/templates/header.php"; ?>
<main id="contents">
	<div class="con_title">
		<div class="box_txt">
			<h1><?php echo $pageName; ?></h1>
		</div>
		<p class="box_img"><img src="<?php echo LOCATION; ?>news/images/title.jpg" alt="<?php echo $pageName; ?>"></p>
	</div>
	<!-- パンくず -->
	<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
		<li property="itemListElement" typeof="ListItem">
			<a property="item" href="<?php echo LOCATION; ?>" typeof="WebPage">
				<span property="name">Home</span>
			</a>
			<meta property="position" content="1">
		</li>
		<li property="itemListElement" typeof="ListItem">
			<span property="name">お知らせ</span>
			<meta property="position" content="2">
		</li>
	</ul>
	<div class="wrap_news">
		<div class="con_news p-news">
			<?php 
			if (isset($view)) {
				echo $view->partial('Common/news_list', ['cms__news_list' => $cms__news_list]);
				echo $view->partial('Common/pagination', ['cms__pagination' => $cms__pagination]);
			}
			?>
		</div>

		<?php include LOCATION_ROOT_DIR."/templates/news_side.php"; ?>
	</div>
</main><!-- /#contents -->
<?php include LOCATION_ROOT_DIR."/templates/footer.php"; ?>
</div>
<!-- #abi_page -->
<?php
if (isset($view)) {
	echo $view->partial('Common/markup', ['cms__markup_data' => $cms__markup_data]);
}
?>
</body>
</html>
