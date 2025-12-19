<?php if (!empty($block) && !empty($block['url']) && !empty($block['button_title'])): ?>
    <?php $color = $block['color'] === 'blue' ? ' c-btn1-rsv' : ''; ?>
    <!-- ボタン -->
    <div class="box_news_parts">
        <p class="btn c-btn1<?php echo $color ?>">
            <a href="<?php echo $block['url'] ?>" target="_blank"><?php echo $block['button_title'] ?></a>
        </p>
    </div>
<?php endif ?>