<?php if (!empty($block) && $block['url'] && !empty($block['btn_title'])): ?>
    <?php $target = $block['is_blank'] ? '_blank' : '_self' ?>
    <!-- テキストリンク -->
    <div class="box_news_parts">
        <p class="txt_link">
            <a href="<?php echo $block['url'] ?>" target="<?php echo $target ?>"><?php echo e($block['btn_title']) ?></a>
        </p>
    </div>
<?php endif; ?>