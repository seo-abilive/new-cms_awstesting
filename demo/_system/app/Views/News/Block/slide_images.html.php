<?php if (is_array($block)): ?>
    <!-- スライダー -->
    <div class="box_news_parts">
        <div id="js-newsSlider" class="box_slide_parts">
            <?php foreach ($block as $value): ?>
                <div class="slide">
                    <p class="photo">
                        <img src="<?php echo $value['file_url'] ?>" alt="">
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="js-arrows slider_dots" id="js-picArrow"></div>
    </div>
    <!-- テキスト -->
<?php endif; ?>