<?php if (!empty($block) && !empty($block['text'])): ?>
    <?php
    switch ($block['style']) {
        case '1':
            $tag = 'h2';
            $class = 'b_title';
            break;
        case '2':
            $tag = 'h3';
            $class = 'b_st';
            break;
        default:
            $tag = 'h4';
            $class = 'b_sst';
            break;
    }
    ?>
    <!-- 見出し -->
    <div class="box_news_parts">
        <<?php echo $tag ?> class="<?php echo $class ?>"><?php echo e($block['text']) ?></<?php echo $tag ?>>
    </div>
<?php endif; ?>