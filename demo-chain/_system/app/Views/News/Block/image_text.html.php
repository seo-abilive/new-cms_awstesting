<?php if (!empty($block)): ?>
    <?php
    $class = $block['layout'] === 'left' ? 'img_l_txt' : 'img_r_txt';
    $imgClass = $block['layout'] === 'left' ? 'box_img' : 'photo';
    ?>
    <!-- 画像+テキスト -->
    <div class="box_news_parts">
        <div class="<?php echo $class ?>">
            <?php if (!empty($block['image'])): ?>
                <p class="<?php echo $imgClass ?>">
                    <img src="<?php echo $block['image']['file_url'] ?>" alt="">
                </p>
            <?php endif; ?>
            <?php if (!empty($block['text'])): ?>
                <div class="txt">
                    <?php echo $block['text'] ?>
                </div>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>