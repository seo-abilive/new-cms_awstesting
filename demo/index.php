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
		<?php include LOCATION_ROOT_DIR . "/templates/header.php"; ?>
		<main id="contents">

			<div class="con_mainimg">
				<!-- MV -->
				<div class="box_img" id="js-mainimg">
					<div class="slide">
						<p class="photo"><img src="<?php echo LOCATION_FILE; ?>images/home/img_mv01.png" data-pc="<?php echo LOCATION_FILE; ?>images/home/img_mv01.png" data-sp="<?php echo LOCATION_FILE; ?>images/home/img_mv01_sp.png" alt="demo"></p>
						<div class="box_txt"></div>
					</div>
					<div class="slide">
						<p class="photo"><img src="<?php echo LOCATION_FILE; ?>images/home/img_mv02.png" data-pc="<?php echo LOCATION_FILE; ?>images/home/img_mv02.png" data-sp="<?php echo LOCATION_FILE; ?>images/home/img_mv02_sp.png" alt="demo"></p>
						<div class="box_txt"></div>
					</div>
					<div class="slide">
						<p class="photo"><img src="<?php echo LOCATION_FILE; ?>images/home/img_mv03.png" data-pc="<?php echo LOCATION_FILE; ?>images/home/img_mv03.png" data-sp="<?php echo LOCATION_FILE; ?>images/home/img_mv03_sp.png" alt="demo"></p>
						<div class="box_txt"></div>
					</div>
				</div>
				<!-- /MV -->

				<!-- 言語切り替え -->
				<div class="c-multilingual">
					<div class="box_multilingual">
						<p class="st accordion js-accordion">
							<span class="inner">
								Language </span>
						</p>
						<ul>
							<li><a href="<?php echo LOCATION; ?>">日本語</a></li>
							<li><a href="<?php echo LOCATION; ?>en/">English</a></li>
							<li><a href="<?php echo LOCATION; ?>zh/">簡体中文</a></li>
							<li><a href="<?php echo LOCATION; ?>tw/">繁體中文</a></li>
							<li><a href="<?php echo LOCATION; ?>ko/">한국어</a></li>
						</ul>
					</div>
				</div>
				<!-- /言語切り替え -->
			</div>


			<div class="wrp_pickup">
				<?php if (isset($view)) {
					echo $view->partial('Banner/list', ['cms__banner_list' => $cms__banner_list]);
				} ?>

				<div class="wrp_news">
					<!-- 新着情報 -->
					<div class="con_news p-news">
						<h2 class="st c-st1">
							<i>NEWS</i>
							<span>お知らせ</span>
						</h2>
						<div class="box_news">
							<?php
							if (isset($view)) {
								echo $view->partial('Common/news_list', ['cms__news_list' => $cms__news_list]);
							}
							?>
						</div>
						<p class="btn c-btn1"><a href="./news/">一覧を見る</a></p>
					</div>
					<!-- /新着情報 -->

					<div class="con_feat">
						<div class="box_photo">
							<h2 class="c-st1">
								<i>FEATURES</i>
								<span>6つの魅力</span>
							</h2>
							<div class="box_txt">
								<p class="st c-jp_h3">快適に安心してお過ごしいただきたい。<br>その想いで皆様をお待ちしております。</p>
							</div>
						</div>
						<ul class="box_feat">
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features01.png" alt="LADIES">
									<p class="ic">
										<em class="num">01．</em>
										<span class="t">LADIES</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">6Fはワンフロア女性専用。女性の一人旅でも安心してご滞在いただけます。</p>
								</div>
							</li>
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features02.png" alt="STAY">
									<p class="ic">
										<em class="num">02．</em>
										<span class="t">STAY</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">
										こだわりのホテルオリジナル寝具を全室に採用。くつろぎのひとときをお過ごしください。
									</p>
								</div>
							</li>
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features03.png" alt="TABLET">
									<p class="ic">
										<em class="num">03．</em>
										<span class="t">TABLET</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">
										ホテル専用タブレットを全室に設置。館内施設の情報やお食事会場の混雑状況、おすすめの観光スポットなどご覧いただけます。</p>
								</div>
							</li>
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features04.png" alt="BREAKFAST">
									<p class="ic">
										<em class="num">04．</em>
										<span class="t">BREAKFAST</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">
										地元のお野菜を使用したこだわりの朝食ブッフェ。<br />
										安心してお食事できるよう感染症対策を徹底した会場設備でお迎えいたします。
									</p>
								</div>
							</li>
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features05.png" alt="WATER">
									<p class="ic">
										<em class="num">05．</em>
										<span class="t">ACCESS</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">
										名古屋駅から徒歩6分の好立地。ビジネス・観光にとても便利です。
									</p>
								</div>
							</li>
							<li>
								<div class="photo">
									<img src="<?php echo LOCATION_FILE; ?>images/home/img_features06.png" alt="SIMPLICITY and SAFETY">
									<p class="ic">
										<em class="num">06．</em>
										<span class="t">SAFETY</span>
									</p>
								</div>
								<div class="box_txt">
									<p class="txt c-jp_b1">
										24時間スタッフ在中&amp;安心のセキュリティシステムを導入しております。
									</p>
								</div>
							</li>
						</ul>
					</div>


					<!-- ホテル情報 -->
					<section class="con_info">
						<h2 class="st c-st1">
							<i>HOTEL INFORMATION</i>
							<span>ホテル情報</span>
						</h2>
						<div class="box_map">
							<p class="photo"><img src="<?php echo LOCATION_FILE; ?>images/home/img_access.jpg" alt=""></p>
							<div class="wrp_map view_pc-tab">
								<div class="inner">
									<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d203.8602119087791!2d136.91009254337712!3d35.16252124035721!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x600370cf27dc8f9f%3A0xb5d43ea05cc9b400!2z44Ki44OT44Oq44OWIOWQjeWPpOWxi-acrOekvg!5e0!3m2!1sen!2sjp!4v1687314612842!5m2!1sen!2sjp" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
								</div>
							</div>
						</div>
						<div class="box_table">
							<table class="c-table sp_block">
								<tr>
									<th>住所</th>
									<td>
										<em>〒460-0008　愛知県名古屋市中区栄5-28-12　名古屋若宮ビル</em>
									</td>
								</tr>
								<tr>
									<th>TEL / FAX</th>
									<td>TEL：<a class="tel" href="tel:0000000000">000-000-0000</a><i class="line">/</i><br class="view_sp">FAX：000-000-0000</td>
								</tr>
								<tr>
									<th>チェックイン / チェックアウト</th>
									<td>15:00&nbsp;/&nbsp;11:00</td>
								</tr>
								<tr>
									<th>客室</th>
									<td>147室</td>
								</tr>
								<tr>
									<th>支払い方法</th>
									<td><span>現金・クレジットカード（VISA、MASTER、JCB、AMEX、DINERS、銀聯カード）</span> <span>QRコード決済（LINE Pay、PayPay、d払い、au PAY、Rpay、ALIPAY、WeChatPay）</span> </td>
								</tr>
								<tr>
									<th>アクセス方法</th>
									<td><span class="bg">地下鉄矢場町3番出口より徒歩2分</span></td>
								</tr>
								<tr>
									<th>駐車場</th>
									<td>無し</td>
								</tr>
								<tr>
									<th>インターネット接続環境</th>
									<td>全室Wi-Fi対応</td>
								</tr>
							</table>
						</div>
					</section>
					<!-- /ホテル情報 -->

				</div>

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