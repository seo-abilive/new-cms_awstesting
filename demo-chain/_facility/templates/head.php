<!DOCTYPE html>
<html class="<?php echo ('' !== (string)$htmlClass) ? 'lang_' . CURRENT_LOCALE . ' ' .  $htmlClass : 'lang_' . CURRENT_LOCALE; ?><?php echo ' facility_' . FACILITY_CODE; ?>" lang="<?php echo HTML_LANG; ?>">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
	<meta charset="utf-8">
	<title><?php echo $meta['title']; ?></title>
	<meta name="keywords" content="<?php echo $meta['keywords']; ?>">
	<meta name="description" content="<?php echo $meta['description']; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=0.0, minimum-scale=1.0 ,viewport-fit=cover">
	<meta name="format-detection" content="telephone=no">
	<link rel="index" href="<?php echo LOCATION_FACILITY; ?>">

	<!-- favicon -->
	<link rel="shortcut icon" href="<?php echo LOCATION_BASE . '_facility/favicon.ico'; ?>">

	<!-- apple-touch-icon -->
	<link rel="apple-touch-icon" href="<?php echo LOCATION_BASE . '_facility/apple-touch-icon.png'; ?>">

	<!-- Open graph tags -->
	<meta property="fb:app_id" content="<?php echo FB_APPID; ?>">
	<meta property="og:site_name" content="<?php echo TITLE; ?>">
	<meta property="og:title" content="<?php echo $meta['title']; ?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo (empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? "http://" : "https://") . @$_SERVER["HTTP_HOST"] . htmlspecialchars(strip_tags(@$_SERVER["REQUEST_URI"])); ?>">
	<meta property="og:description" content="<?php echo $meta['description']; ?>">
	<?php if (FACILITY_CODE === 'karasuma' && $page === 'homepage') : ?>
		<meta property="og:image" content="<?php echo LOCATION_BASE . '_facility/ogp_shijo.png'; ?>">
	<?php endif; ?>
	<?php if (FACILITY_CODE === 'karasuma' && $page !== 'homepage') : ?>
		<meta property="og:image" content="<?php echo LOCATION_BASE . '_facility/ogp_shijo_lower.png'; ?>">
	<?php endif; ?>
	<?php if (FACILITY_CODE === 'karasumagojo' && $page === 'homepage') : ?>
		<meta property="og:image" content="<?php echo LOCATION_BASE . '_facility/ogp_gojo.png'; ?>">
	<?php endif; ?>
	<?php if (FACILITY_CODE === 'karasumagojo' && $page !== 'homepage') : ?>
		<meta property="og:image" content="<?php echo LOCATION_BASE . '_facility/ogp_gojo_lower.png'; ?>">
	<?php endif; ?>