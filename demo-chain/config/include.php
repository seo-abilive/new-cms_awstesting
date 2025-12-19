<?php
	//言語設定用
	if(defined('CURRENT_LOCALE')){
		$cms__lang = CURRENT_LOCALE;
	} else {
		$cms__lang = 'jp';
	}

	switch($cms__lang){
		case 'en':
			$html_lang = 'en';
			break;
		case 'cn':
			$html_lang = 'zh-Hans';
			break;
		case 'tw':
			$html_lang = 'zh-Hant';
			break;
		case 'ko';
			$html_lang = 'ko';
			break;
		default:
			$html_lang = 'ja';
	}

	//関数の定義やその他設定
	include __DIR__.'/setting.php';

	//定数の定義
	include __DIR__.'/config.php';

	//metaタグに関する設定
	if(isset($cms__lang) && '' !== (string)$cms__lang && 'jp' !== $cms__lang) {
		include __DIR__ . '/meta.' . $cms__lang . '.php';
	} else {
		include __DIR__ . '/meta.php';
	}

	//headの読み込み
	include __DIR__.'/../templates/head.php';


?>