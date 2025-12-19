<?php
$page = 'contact';
$pageName = 'お問い合わせ';
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
			<p class="box_img"><img src="images/title.jpg" alt="<?php echo $pageName; ?>"></p>
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
				<span property="name">お問い合わせ</span>
				<meta property="position" content="2">
			</li>
		</ul>
		<div class="con_form">
			<div class="box_int">
				<p class="txt">
					よくいただくお問い合わせ内容をおまとめしております。<br>事前にご確認いただければ、解決策が見つかるかもしれません。ぜひご活用くださいませ。
				</p>
				<p class="btn c-btn1"><a href="<?php echo LOCATION;?>faq/">よくあるご質問</a></p>
			</div>

			<ul class="box_note">
				<li>・お問い合わせ内容の確認後、担当者よりご回答をさせていただきます。（土・日・祝・年末年始を除く）<br>なお、ご回答までに多少の時間を要する場合がございますので、あらかじめご了承ください。お急ぎの場合は、ホテルにお電話にてお問い合わせください。</li>
				<li>・メールアドレスが正しくない場合は、ご返信ができません。</li>
				<li>・お問い合わせ内容によりましてはご返信ができない場合もございますので、ご了承ください。</li>
				<li>・ホテルにご宿泊のお客様宛のご伝言にはご利用になれません。</li>
				<li>・ご入力いただきました個人情報はお問い合わせに対するご回答にのみ使用させていただきます。他の目的には一切使用いたしません。</li>
			</ul>

			<div class="panel">
				<div class="box_panel" id="panel02">
					<div class="box_form">
						<p class="txt_attention">
							<i>※</i> 印は必須項目です。必ずご記入ください。（ご予約、キャンセル、お急ぎのご用件は各ホテルにご連絡ください。）
						</p>

						<?php echo CONTACT_WIDGET_CODE; ?>
					</div>
				</div>
			</div>
		</div>
	</main><!-- /#contents -->
	<?php include LOCATION_ROOT_DIR."/templates/footer.php"; ?>
</div>
<!-- #abi_page -->

</body>
</html>