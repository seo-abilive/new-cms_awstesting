<?php if (!empty($cms__emergency_news)) : ?>
    <?php
    $isLink = $cms__emergency_news['page_type'] === 'link';
    $url = $isLink ? $cms__emergency_news['link']['link_url'] : $cms__emergency_news['pdf']['file_url'];
    $target = !$isLink || $cms__emergency_news['link']['is_blank'] ? '_blank' : '_self';
    ?>
    <div class="con_attention js-closeBox">
        <p>
            <a href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                <?php echo $cms__emergency_news['title']; ?>
            </a>
        </p>
        <i class="ic-clearclose btn js-closeBtn"></i>
    </div>
<?php endif; ?>