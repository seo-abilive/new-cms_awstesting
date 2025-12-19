<?php
$page = 'facilities';
$pageName = '施設・設備';
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
		// modal
		$(".js-modal").modaal({
			type: 'video'
		});
		$('.modal_map').find('.close').click(function() {
			$(".js-modal").modaal('close');
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
			<div class="con_title">
				<div class="box_txt">
					<h1><?php echo $pageName; ?></h1>
				</div>
				<p class="box_img">
					<img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/26/1920x390_ea1e9d427fb5664c32c517a73e421e58_d6166aa223a8ad8d50b88c1495135aff525d8065.jpg" alt="FACILITIES">
				</p>
			</div>

			<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION_GENERAL; ?>" typeof="WebPage">
						<span property="name" content="HOME"><i class="ic-home"></i></span>
					</a>
					<meta property="position" content="1">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION; ?>" typeof="WebPage">
						<span property="name">THE DEMO HOTEL</span>
					</a>
					<meta property="position" content="2">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION_FACILITY; ?>" typeof="WebPage">
						<span property="name">HOTEL A</span>
					</a>
					<meta property="position" content="3">
				</li>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?php echo $pageName; ?></span>
					<meta property="position" content="4">
				</li>
			</ul><!-- /.topicpath -->

			<div class="con_fac">
				<h2 class="st">館内施設</h2>
				<ul class="box_fac">
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_607c772ad9732e30aaf4589f2f2718d81d9d9e76.jpg" alt="フロント・ロビー"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">フロント・ロビー</h3>
							<p class="txt">24時間フロント対応。また、ロビー・ラウンジは24時間ご利用可能です</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/02/21/384x240_ea1e9d427fb5664c32c517a73e421e58_499ad42ead136df1dbbd4aa95551b3323ef40846.jpg" alt="セルフチェックイン・チェックアウト端末 "></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">セルフチェックイン・チェックアウト端末 </h3>
							<p class="txt">お客様ご自身が簡単な操作でチェックイン・チェックアウト手続きを行えます。</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_4be3ff467b443d1d6be01652f7039828de3f9167.jpg" alt="シャワールーム"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">シャワールーム</h3>
							<p class="txt">男女別シャワールームをご用意しており、客室に設置のタブレットで混雑状況をご確認頂けます。シャワールーム内にはコインランドリーも併設しています。<br>&nbsp;</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_e525b7d91226166d7f06b69f7c58207d21048b4b.jpg" alt="Wi-Fi"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">Wi-Fi</h3>
							<p class="txt">全館・全室にて無料でWi-Fiをご利用いただけます。</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_6e9b9c37b110f886091efbbfab543344f2fb96b6.jpg" alt="非接触ICカードキー"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">非接触ICカードキー</h3>
							<p class="txt">全室に非接触ICカードキーを導入しております。またエレベーターはキータッチシステムを採用しカードキーをタッチして客室階へ上がることができます。ゲスト以外の侵入を防ぐ高いセキュリティーで女性も安心してご利用いただけます。</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_bd1997693bdcac1ea7e0d432b8a0be5487e5d7e7.jpg" alt="自動販売機コーナー"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">自動販売機コーナー</h3>
							<p class="txt">ドリンク、軽食、アメニティなどの自動販売機をご用意しています。</p>
						</div>
					</li>
					<li>
						<div class="box_photo">
							<p class="floor">1階</p>
							<p class="photo"><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/09/05/384x240_ea1e9d427fb5664c32c517a73e421e58_1650ca563895696a372edb9db538aa6891c636ad.jpg" alt="コインランドリー"></p>
						</div>
						<div class="box_txt">
							<h3 class="sst">コインランドリー</h3>
							<p class="txt">長期滞在や急なご宿泊にも乾燥機能付のコインランドリーをご用意しておりますので安心してご利用頂けます。1階のシャワールーム内に設置しており、客室に設置のタブレットで稼働状況をご確認頂けます。</p>
						</div>
					</li>
				</ul>
				<p class="note_fac">※画像はイメージです</p>
			</div>

			<div class="con_service">
				<h2 class="st">サービス</h2>
				<div class="box_service">
					<div class="inner">
						<h3 class="sst">共用シャワールーム設備</h3>
						<ul>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_c98b9d28bf77431e292e80b8aa3d42dbf617754e.jpg" alt="Hair dryer"></i><em>ドライヤー</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_bf186a315bd022976e5e30e20f0cf4c04d40268f.jpg" alt="2-in-1 conditioner &amp; shampoo"></i><em>リンスイン<br>
									シャンプー</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_40966e644b617c41c9fb0d4dd6575d49b1475f62.jpg" alt="Body wash"></i><em>ボディソープ</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_0aac49bc3d906b83316b2de5193364c01a7fc24c.jpg" alt="Hand soap"></i><em>ハンドソープ</em>
							</li>
						</ul>
						<p class="note">※ホテルオリジナル上下セパレートパジャマは1階フロント横にてご用意しております。</p>
					</div>
				</div>
				<div class="box_service">
					<div class="inner">
						<h3 class="sst">客室設備</h3>
						<ul>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_4d805910d87000ece7065911277d9d7b51280f4c.jpg" alt=""></i><em>全室Wi-Fi対応</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_03931e4b08d28a3a2bf51b90210cd9685fa2408f.jpg" alt=""></i><em>個別エアコン</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_b6a6886a7388c88e5d627a948c36979830a4c2ea.jpg" alt=""></i><em>客室用<br>
									タブレット</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_c1796fddf987a114955d27fc97a3b923d91d45a2.jpg" alt=""></i><em>バスタオル</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_e9516e7e1af533ace2eb654843947131c3bafb01.jpg" alt=""></i><em>フェイスタオル</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_a0d993e8e2b35a8fd747c0ec189a15cf7a4a1e11.jpg" alt=""></i><em>歯ブラシ</em>
							</li>
							<li>
								<i><img src="https://demo.abi-cms.net/hotel_full/datas/cache/images/2023/01/25/150x90_ea1e9d427fb5664c32c517a73e421e58_a4e60be0a33931e3907243e86a1c9722c8ce144b.jpg" alt=""></i><em>スリッパ</em>
							</li>
						</ul>
					</div>
				</div>
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