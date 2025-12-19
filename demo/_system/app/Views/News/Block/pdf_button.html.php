<?php if (!empty($block) && !empty($block['file']) && !empty($block['button_title'])): ?>
    <!-- PDF -->
    <div class="box_news_parts">
        <p class="pdf_link">
            <a href="<?php echo $block['file']['file_url'] ?>" target="_blank"><?php echo e($block['button_title']) ?></a>
        </p>
    </div>
<?php endif; ?>