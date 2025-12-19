<?php
$page = 'contact';
$pageName = 'お問い合わせ';
include realpath(__DIR__ . '/../config/include.php');
?>
<!-- *** stylesheet *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_css.php"; ?>
<!-- *** stylesheet *** -->
<link href="<?php echo echo_version(LOCATION_FILE . 'css/' . $page . '.css', LOCATION_FILE_DIR); ?>" rel="stylesheet" media="all">
<!-- *** javascript *** -->
<?php include LOCATION_ROOT_DIR . "/templates/common_js.php"; ?>
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
				<p class="box_img"><img src="<?php echo LOCATION_BASE; ?>contact/images/img_title.jpg" alt="<?php echo $pageName; ?>"></p>
			</div>
			<ul class="topicpath" vocab="https://schema.org/" typeof="BreadcrumbList">
				<li property="itemListElement" typeof="ListItem">
					<a property="item" href="<?php echo LOCATION_GENERAL; ?>" typeof="WebPage">
						<span property="name">HOME</span>
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
					<span property="name"><?php echo $pageName; ?></span>
					<meta property="position" content="3">
				</li>
			</ul><!-- /.topicpath -->

			<div class="con_form">
				<div class="box_int">
					<p class="txt">
						よくいただくお問い合わせ内容をおまとめしております。<br>
						事前にご確認いただければ、解決策が見つかるかもしれません。ぜひご活用くださいませ。
					</p>
					<p class="btn c-btn1"><a href="<?php echo LOCATION; ?>faq/">よくあるご質問</a></p>
				</div>

				<ul class="box_note">
					<li>・お問い合わせ内容の確認後、担当者よりご回答をさせていただきます。（土・日・祝・年末年始を除く）
						なお、ご回答までに多少の時間を要する場合がございますので、あらかじめご了承ください。お急ぎの場合は、ホテルにお電話にてお問い合わせください。</li>
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
							<form name="" method="post" action="./?" id="contact_form">
								<table id="js_tbl">
									<tbody>
										<tr class="js_rec">
											<th><i>※</i>お問い合わせ項目</th>
											<td>
												<select id="contact_type" name="contact_type">
													<option value="">（選択してください）</option>
													<option value="1">ABILIVE HOTEL ホテルA</option>
													<option value="2">ABILIVE HOTEL ホテルB</option>
													<option value="3">ABILIVE HOTELS CLUBについて</option>
													<option value="4">ホテル開発・新規出店について</option>
													<option value="5">商品、サービスの営業提案について</option>
													<option value="6">法人宿泊契約について</option>
													<option value="7">その他</option>
												</select>
											</td>
										</tr>
										<tr>
											<th><i>※</i>お名前</th>
											<td>
												<input type="text" id="name" name="name" placeholder="例）アビリブ 太郎" class="l">
											</td>
										</tr>
										<tr>
											<th>会社名</th>
											<td>
												<input type="text" id="company" name="company" placeholder="例）株式会社アビリブホテルマネジメント" class="m">
											</td>
										</tr>
										<tr>
											<th><i>※</i>メールアドレス</th>
											<td>
												<input type="text" id="email" name="email" data-required="true" data-rule="email" data-name="メールアドレス" class="m" placeholder="例）taro@demo-hotels.com">
											</td>
										</tr>
										<tr>
											<th><i>※</i>メールアドレス（確認）</th>
											<td>
												<input type="text" id="email_confirmation" name="email_confirmation" data-required="true" data-rule="email" data-name="メールアドレス" class="m" placeholder="例）taro@demo-hotels.com">
											</td>
										</tr>
										<tr>
											<th><i>※</i>電話番号</th>
											<td>
												<input type="text" id="phone" name="phone" placeholder="例）03-1234-5678" class="l">
											</td>
										</tr>
										<tr>
											<th><i>※</i>お問い合わせ内容</th>
											<td>
												<textarea id="content" name="content" placeholder="具体的なお問い合わせ内容をご記入ください。" cols="30" rows="10"></textarea>
											</td>
										</tr>
										<tr class="style1">
											<th><i>※</i>プライバシーポリシー</th>
											<td colspan="2">
												<label for="privacy" class="">
													<input type="checkbox" id="agree_0" name="agree[]" value="1"> </label>
												<p>
													プライバシーポリシーに同意する<br class="view_sp">（プライバシーポリシーは<a href="/privacy/" target="_blank">こちら</a>） </p>
												<br>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="wrp_attention">
									お客様へご返信は「@abilive-group.jp」及び「@abilive-hotels.com」のドメインから送信致します。<br>
									メールを受信できない場合は、メールの受信拒否が設定されている場合があります。<br>
									受信設定でドメイン指定受信を設定している方は、「@abilive-group.jp」及び「@abilive-hotels.com」を個別に受信したいドメインに設定してください。 </div>

								<div style="margin: 1em 0">
									<div id="inline-badge"></div>
								</div>

								<div class="wrp_btn">
									<p class="c-btn1" id="js_submit"><span>入力内容を確認する</span></p>
								</div>
								<input type="hidden" id="recaptcha_token" name="recaptcha_token"> <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"> <input type="hidden" id="_token" name="_token" value="QtaRgqh6xNrz21cXEJ11zPwrsa288cTnnbU4-QabiAw">
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php include LOCATION_ROOT_DIR . "/templates/common_contents.php"; ?>

		</main><!-- /#contents -->
		<?php include LOCATION_ROOT_DIR . "/templates/footer.php"; ?>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/css/modaal.min.css" integrity="sha384-fCp5T7Jho7XA7FhIWeqR9vj8ZxGoporWYpDGXc3XQPU93zEKdK8cESf1l3YO/lRk" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/modaal@0.4.4/dist/js/modaal.min.js" integrity="sha384-N1NyMYVZ/NtKw105M5oKbEbqMy33NawGjCQKMdPC4aH/CbW0NR0Z9hZ5F1AmOkIC" crossorigin="anonymous"></script>
	<!-- #abi_page -->
</body>

</html>