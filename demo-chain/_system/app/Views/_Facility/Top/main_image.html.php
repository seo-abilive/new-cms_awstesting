<?php foreach ($cms__top_page_setting['pc_main_images'] as $key => $mainImage) : ?>
    <div class="slide">
        <p class="photo">
            <img src="<?php echo $mainImage['file_url']; ?>" data-pc="<?php echo $mainImage['file_url']; ?>" data-sp="<?php echo $cms__top_page_setting['sp_main_image'][$key]['file_url']; ?>" alt="<?php echo e($mainImage['alt_text']); ?>">
        </p>
    </div>
<?php endforeach; ?>