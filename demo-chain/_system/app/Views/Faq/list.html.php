<?php if (!empty($cms__faq)) : ?>
    <?php foreach ($cms__faq as $faq) : ?>
        <div class="box_faq" id="link<?php echo $faq['category']['id'] ?>">
            <h3><span><?php echo e($faq['category']['title']) ?></span></h3>
            <?php foreach ($faq['items'] as $item) : ?>
                <div class="faq_det" id="question_<?php echo $item['id'] ?>">
                    <p class="accordion"><span><em><?php echo e($item['question']) ?></em></span></p>
                    <div class="inner">
                        <div class="answer"><?php echo $item['answer'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>