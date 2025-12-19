<?php if (!empty($block)): ?>
    <?php $imgNum = count($block); ?>
    <!-- 画像 -->
    <div class="box_news_parts" data-cms-field="customblock.images1__c07813331129f14bc241f70c05f3f15807895c40">
        <ul class="b_img col_<?php echo sprintf('%02d', $imgNum) ?>">
            <?php foreach ($block as $value): ?>
                <li>
                    <p class="photo">
                        <img src="<?php echo $value['file_url'] ?>" alt="">
                    </p>
                    <?php if (!empty($value['caption'])): ?>
                        <p class="txt_caption"><?php echo e($value['caption']) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>