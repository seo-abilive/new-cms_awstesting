<?php
$page = 'news';
$pageName = $cms__page_setting['title'] ?? 'お知らせ';
include realpath(__DIR__ . '/../config/include.php');
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<link href="<?php echo echo_version(LOCATION_FACILITY_FILE . 'css/news.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
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

			<div class="wrap_news">
				<div class="con_news p-news">

					<div class="box_news">
						<?php if (isset($view)) {
							echo $view->partial('Common/news_list', ['cms__news_list' => $cms__news_list, 'base_url' => LOCATION_FACILITY]);
						} ?>

					</div>
					<?php if (isset($view)) {
						echo $view->partial('Common/pagination', ['cms__pagination' => $cms__pagination]);
					} ?>
				</div>
				<?php include LOCATION_ROOT_DIR . "/templates/news_side.php"; ?>
			</div>

			<?php include LOCATION_ROOT_DIR . "../../templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/css/modaal.min.css" integrity="sha384-fCp5T7Jho7XA7FhIWeqR9vj8ZxGoporWYpDGXc3XQPU93zEKdK8cESf1l3YO/lRk" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/js/modaal.min.js" integrity="sha384-N1NyMYVZ/NtKw105M5oKbEbqMy33NawGjCQKMdPC4aH/CbW0NR0Z9hZ5F1AmOkIC" crossorigin="anonymous"></script>
	<!-- #abi_page -->
</body>

</html>