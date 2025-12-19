<?php if (!empty($cms__page_setting['main_image'])): ?>
    <div class="con_title">
        <div class="box_txt">
            <h1><?php echo $pageName; ?></h1>
        </div>
        <p class="box_img">
            <img src="<?php echo $cms__page_setting['main_image']['file_url']; ?>" alt="<?php echo e($cms__page_setting['title']); ?>">
        </p>
    </div>
<?php endif; ?>