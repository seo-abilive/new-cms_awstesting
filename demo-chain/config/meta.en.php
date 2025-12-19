<?php
/*

	meta.php
	メタ情報を管理するファイルです

	$meta['title'] →　title
	$meta['keywords'] →　keywords
	$meta['description'] →　description
	$meta['H1'] →　h1


*/


/*--	Settings
--------------------------------------------------*/

//共通文言
define('TITLE', FACILITY_NAME . ' 【公式】');
define('KEYWORDS', 'サンプルグループ,サンプルホテルズ,THE DEMO HOTEL,京都,四条,烏丸,宿泊,ホテル,予約');
define('DESCRIPTION', 'THE DEMO HOTEL 京都四条烏丸は宿泊における快適さと機能性の両立を、これまでにないカタチで実現した宿泊特化型のホテルです。');
define('H1', 'ページh1ページh1ページh1');


//初期文言
$meta = array(
	'title' => TITLE,
	'keywords' => KEYWORDS,
	'description' => DESCRIPTION,
	'h1' => H1
);


/*--	Main page
--------------------------------------------------*/

#トップページ
if(isset($cms__meta) && $page == 'homepage') {
	$meta['title'] = $cms__meta['title'];
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = H1;
}

#ホテル一覧
if(isset($cms__meta) && $page == 'hotel') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のホテル一覧';
}

#過ごし方
if(isset($cms__meta) && $page == 'stay') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'の過ごし方';
}

#料金について
if(isset($cms__meta) && $page == 'price') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'の料金について';
}

#料金について
if(isset($cms__meta) && $page == 'price') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'の料金について';
}

#料金について
if(isset($cms__meta) && $page == 'price') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'の料金について';
}

#お知らせ
if(isset($cms__meta) && $page == 'news') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のお知らせ';
}

#お知らせ記事詳細
if(isset($cms__meta) && $page == 'news_detail') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のお知らせ ' . $cms__meta['title'];
}

#イベント・特集
if(isset($cms__meta) && $page == 'events') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のイベント・特集';
}

#イベント・特集記事詳細
if(isset($cms__meta) && $page == 'events_detail') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のイベント・特集 ' . $cms__meta['title'];
}

#スタッフブログ
if(isset($cms__meta) && $page == 'blog') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のスタッフブログ';
}

#よくあるご質問
if(isset($cms__meta) && $page == 'faq') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のよくあるご質問';
}

#インフォメーション
if(isset($cms__meta) && $page == 'information') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のインフォメーション';
}

#ご利用規約
if(isset($cms__meta) && $page == 'terms') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のご利用規約';
}

#テレワーク
if(isset($cms__meta) && $page == 'telework') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のテレワーク';
}

#テレワーク
if(isset($cms__meta) && $page == 'telework') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = FACILITY_SHORT_NAME . 'のテレワーク';
}

#お問い合わせ
if(isset($cms__meta) && $page == 'contact') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = $cms__meta['description'];
	$meta['h1'] = 'サンプルホテルズへのお問い合わせ';
}

#お問い合わせ　確認
if(isset($cms__meta) && $page == 'contact_check') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = 'サンプルホテルズのお問い合わせ確認ページです。';
	$meta['h1'] = 'サンプルホテルズへのお問い合わせ確認ページ';
}

#お問い合わせ　完了
if(isset($cms__meta) && $page == 'contact_thanks') {
	$meta['title'] = $cms__meta['title'] . ' | '.TITLE;
	$meta['keywords'] = $cms__meta['keywords'];
	$meta['description'] = 'サンプルホテルズのお問い合わせ完了ページです。';
	$meta['h1'] = 'サンプルホテルズへのお問い合わせ完了ページ';
}

#コピー
if($page == 'copy') {
	$meta['title'] = 'コピー | '.TITLE;
	$meta['keywords'] = KEYWORDS.',コピー';
	$meta['description'] = DESCRIPTION;
	$meta['h1'] = H1;
}


/*--    Error page
--------------------------------------------------*/

#error - 403
if($page == 'misc_403') $meta['title'] = 'Error 403 Forbidden アクセスが制限されています | '.title;

#error - 404
if($page == 'misc_404') $meta['title'] = 'Error 404 Page Not Found ページが見つかりません | '.title;

#error - 500
if($page == 'misc_500') $meta['title'] = 'Error 500 Internal Server Error サーバーエラーが出ています | '.title;


?>
