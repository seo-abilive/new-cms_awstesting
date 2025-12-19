<?php if (!empty($cms__banner_list)) : ?>
    <!-- バナー -->
    <div class="con_pickup">
        <div class="box_img" id="js-picSlider">
            <?php foreach ($cms__banner_list as $banner) : ?>
                <?php
                $url = $banner['url'] ?? null;
                $target = $banner['is_blank'] ? '_blank' : '_self';
                ?>
                <div class="slide">
                    <?php if ($url): ?>
                        <a href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                        <?php endif; ?>
                        <img src="<?php echo $banner['image']['file_url'] ?>" alt="<?php echo e(($banner['image']['alt_text'] ?? '')); ?>">
                        <?php if ($url): ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="js-arrows slider_dots" id="js-picArrow"></div>
    </div>
    <!-- /バナー -->
<?php endif; ?>