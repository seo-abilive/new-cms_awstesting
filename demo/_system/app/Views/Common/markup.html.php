<?php if (!empty($cms__markup_data)) : ?>
<script type="application/ld+json">
    <?php echo json_encode($cms__markup_data['contents'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>