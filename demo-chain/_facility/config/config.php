<?php

/*	Constant
--------------------------------------------------------------*/
//言語設定
if (!defined('DEFAULT_LOCALE')) {
	define('DEFAULT_LOCALE', 'ja');
}

if (!defined('CURRENT_LOCALE')) {
	define('CURRENT_LOCALE', DEFAULT_LOCALE);
}

switch (CURRENT_LOCALE) {
	case 'en':
		define('HTML_LANG', 'en');
		break;
	case 'cn':
		define('HTML_LANG', 'zh-cn');
		break;
	case 'tw':
		define('HTML_LANG', 'zh-tw');
		break;
	case 'ko':
		define('HTML_LANG', 'ko');
		break;
	default:
		define('HTML_LANG', 'ja');
}

//ローカル&テスト
if (!defined('LOCATION')) {
	if (empty($_SERVER['HTTPS'])) {
		//define('LOCATION', 'http://' . $_SERVER['SERVER_NAME'] . ':8083' . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . "/.."))) . '/');
		define('LOCATION', 'http://localhost:8083/');
	} else {
		define('LOCATION', 'https://' . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . ".."))) . '/');
	}
}

if (!defined('LOCATION_GENERAL_BASE')) {
	if (empty($_SERVER['HTTPS'])) {
		define('LOCATION_GENERAL_BASE', 'http://' . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . "/../../.."))) . '/');
	} else {
		define('LOCATION_GENERAL_BASE', 'https://' . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . "/../../.."))) . '/');
	}
}

if (!defined('LOCATION_GENERAL')) {
	define('LOCATION_GENERAL', LOCATION);
}

define('LOCATION_GENERAL_FILE', LOCATION_GENERAL_BASE . 'files/');

define('LOCATION_GENERAL_LANG_FILE', LOCATION_GENERAL . 'files/');

// 公開の際は絶対パスへ
// if(empty($_SERVER['HTTPS'])){
// 	define('LOCATION','http://www.example.com/');
// } else {
// 	define('LOCATION','https://www.example.com/');
// }
if (!defined('LOCATION_BASE')) {
	define('LOCATION_BASE', LOCATION);
}

if (!defined('LOCATION_FILE')) {
	define('LOCATION_FILE', LOCATION_BASE . 'files/');
}

if (!defined('LOCATION_LANG_FILE')) {
	define('LOCATION_LANG_FILE', LOCATION . 'files/');
}

/*
 * 施設サイト
 */
if (!defined('LOCATION_FACILITY')) {
	define('LOCATION_FACILITY', LOCATION . $cms__facility_alias . '/');
}

if (!defined('LOCATION_FACILITY_BASE')) {
	define('LOCATION_FACILITY_BASE', LOCATION_BASE);
}

define('LOCATION_FACILITY_FILE', LOCATION_BASE . '_facility/files/');

define('LOCATION_FACILITY_LANG_FILE', LOCATION . '_facility/files/');

define('LOCATION_ROOT_DIR', realpath(__DIR__ . '/../'));
define('LOCATION_FILE_DIR', realpath(__DIR__ . '/../files/'));

/* normalPages */
$PageList = array(
	'ACCESS',			#アクセス(access/index.php)
	'ROOMS',			#客室(rooms/index.php)
	'NEWS',				#お知らせ　一覧　(news/index.php)
	'NEWS__DETAIL',		#お知らせ 詳細 (news/detail.php)
	'EVENTS',			#イベント・特集 一覧(events/index.php)
	'EVENTS__DETAIL',	#イベント・特集 詳細 (events/detail.php)
	'BLOG',				#スタッフブログ(blog/index.php)
	'blog__detail',		#スタッフブログ 詳細(blog/index.php)
	'FACILITIES',		#施設・設備(facilities/index.php)
	'BREAKFAST',		#朝食(breakfast/index.php)
	'DAYUSE',			#デイユース（日帰りプラン）(dayuse/index.php)
	'GALLERY',			#フォトギャラリー(gallery/index.php)
	'PRICE',			#料金について(price/index.php)
	'FAQ',			    #よくあるご質問(faq/index.php)
	'STAY',			    #ホテルでの過ごし方(stay/index.php)

	'copy',				#コピー1　(copy/index.php)
	'copy__copy',		#コピー2 (copy/copy.php)
	'copy_copy',		#コピー3 (copy/copy/index.php)
	'copy_copy__copy'	#コピー4 (copy/copy/copy.php)
);

definitionLink($PageList, false);

/* Reservations */
define('LOCATION_RSV', '');
define('LOCATION_PLAN', '');
define('LOCATION_CHANGE', '');
define('LOCATION_CANCEL', '');
// define('LOCATION_LOGIN', '');


/* リンク振り分け */
if (!$phone) {
	//pcSite
	define('LOCATION_XXX', '');
} else {
	//spSite
	define('LOCATION_XXX', '');
}


/* Other */
define('FB_APPID', '');
define('LOCATION_TEL', 'tel:0752831110');
define('PLACEHOLDER_IMAGE', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');

if (!defined('FACILITY_CODE')) {
	define('FACILITY_CODE', '_facility');
}

if (!defined('FACILITY_NAME')) {
	define('FACILITY_NAME', 'THE DEMO HOTEL');
}

if (!defined('FACILITY_SHORT_NAME')) {
	define('FACILITY_SHORT_NAME', 'THE DEMO HOTEL');
}
