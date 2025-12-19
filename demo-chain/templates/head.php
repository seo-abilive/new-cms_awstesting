<!DOCTYPE html>
<html class="<?php echo ('' !== (string)$htmlClass) ? 'lang_' . CURRENT_LOCALE . ' ' .  $htmlClass : 'lang_' . CURRENT_LOCALE; ?>" lang="<?php echo HTML_LANG; ?>">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<meta charset="utf-8">
<title><?php echo $meta['title']; ?></title>
<meta name="keywords" content="<?php echo $meta['keywords']; ?>">
<meta name="description" content="<?php echo $meta['description']; ?>">
<meta name="viewport" content="width=device-width, initial-scale=0.0, minimum-scale=1.0 ,viewport-fit=cover">
<meta name="format-detection" content="telephone=no">
<link rel="index" href="<?php echo LOCATION; ?>">

<!-- favicon -->
<link rel="shortcut icon" href="<?php echo LOCATION_BASE.'favicon.ico'; ?>">

<!-- apple-touch-icon -->
<link rel="apple-touch-icon" href="<?php echo LOCATION_BASE.'apple-touch-icon.png'; ?>">

<!-- Open graph tags -->
<meta property="fb:app_id" content="<?php echo FB_APPID; ?>">
<meta property="og:site_name" content="<?php echo TITLE; ?>">
<meta property="og:title" content="<?php echo $meta['title']; ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo (empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? "http://" : "https://") . @$_SERVER["HTTP_HOST"] . htmlspecialchars( strip_tags( @$_SERVER["REQUEST_URI"] )); ?>">
<meta property="og:description" content="<?php echo $meta['description']; ?>">

<?php if($page === 'homepage') : ?>
<meta property="og:image" content="<?php echo LOCATION_BASE.'ogp.png'; ?>">
<?php else : ?>
<meta property="og:image" content="<?php echo LOCATION_BASE.'ogp_lower.png'; ?>">
<?php endif; ?>

<script type="application/ld+json">
{
	"@context" : "http://schema.org",
	"@type" : "Organization",
	"url" : "<?php echo LOCATION_BASE; ?>",
	"logo" : "<?php echo LOCATION_FILE; ?>images/footer/ic_poc.png"
}
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K8FKK4L');</script>
<!-- End Google Tag Manager -->

<script async src="https://control.abi-chat.net/client/abichat.js" id="abichat-script" data-uuid="4deac990-5ff6-4eab-91a0-cd3e1d12bab3" data-facility="abilive_demo_chain_hotel"></script>
