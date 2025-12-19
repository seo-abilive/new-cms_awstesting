<?php 
	$page = 'misc_403';
	$pageName = 'エラー';
	include realpath(__DIR__.'/../config/include.php');
?>
<meta name="robots" content="nofollow">
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR."/templates/common_css.php"; ?>
<!-- *** stylesheet *** -->
<link href="<?php echo echo_version(LOCATION_FILE.'css/misc.css',LOCATION_FILE_DIR);?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR."/templates/common_js.php"; ?>
</head>

<body id="<?php echo $page; ?>" class="sub">
<?php include LOCATION_ROOT_DIR."/templates/gtm.php"; ?>
<div id="abi_page">
<?php include_once LOCATION_ROOT_DIR.'/files/images/common/symbol-defs.svg'; ?>
<?php include LOCATION_ROOT_DIR."/templates/header.php"; ?>
<main id="contents">

	<div class="con_title">
		<div class="box_txt">
			<h1><?php echo $pageName; ?></h1>
		</div>
	</div>
	<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
		<li property="itemListElement" typeof="ListItem">
			<a property="item" href="<?php echo LOCATION; ?>" typeof="WebPage">
				<span property="name" content="<?php echo e($view['translator']->trans('global_menu.home', [], 'common')); ?>"><i class="ic-home"></i></span>
			</a>
			<meta property="position" content="1">
		</li>
		<li property="itemListElement" typeof="ListItem">
			<span property="name"><?php echo $pageName; ?></span>
			<meta property="position" content="2">
		</li>
	</ul><!-- /.topicpath -->

	<section class="con_misc">
		<h3>このページはアクセスが制限されております。</h3>
		<p>
			お探しのページはアクセスが制限されております。<br>
			お手数ではございますが、<a href="<?php echo LOCATION;?>">トップページ</a>にて目的のページをお探しください。
		</p>
	</section><!-- /.con_misc -->

	<?php include LOCATION_ROOT_DIR."/templates/common_contents.php"; ?>

</main><!-- /#contents -->
<?php include LOCATION_ROOT_DIR."/templates/footer.php"; ?>
</div>
<!-- #abi_page -->
</body>
</html>