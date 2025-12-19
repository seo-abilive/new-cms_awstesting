<?php $base_url = $base_url ?? LOCATION . 'news/'; ?>

<div class="box_detail">
    <div class="wrp_inf">
        <p class="dat"><?php echo (new DateTime($news['public_date']))->format('Y.m.d') ?></p>
        <h1 class="wrp_tit"><?php echo e($news['title']) ?></h1>
        <div class="wrp_det">
            <?php
            if (isset($news['custom_block']) && is_array($news['custom_block'])) {
                foreach ($news['custom_block'] as $block) {
                    $key = key($block);
                    echo $view->partial("News/Block/{$key}", ['block' => $block[$key]]);
                }
            }
            ?>
        </div>
    </div>
</div>

<div class="box_arrow">
    <?php if ($sibLings['previous']) : ?>
        <p class="prev slick-arrow">
            <a href="<?php echo $base_url . $sibLings['previous']['id'] . '/'; ?>">
                <i class="ic-chevron-left"></i>PREV
            </a>
        </p>
    <?php endif; ?>
    <p class="btn">
        <a href="<?php echo $base_url; ?>">記事一覧<span class="view_pc-tab">に戻る</span></a>
    </p><!-- /.con_pager -->
    <?php if ($sibLings['next']) : ?>
        <p class="next slick-arrow">
            <a href="<?php echo $base_url . $sibLings['next']['id'] . '/'; ?>">
                NEXT<i class="ic-chevron-right"></i>
            </a>
        </p>
    <?php endif; ?>
</div>