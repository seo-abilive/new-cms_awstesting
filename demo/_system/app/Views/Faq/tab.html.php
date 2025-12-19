<?php if (!empty($cms__faq)) : ?>
    <ul>
        <?php foreach ($cms__faq as $faq) : ?>
            <li class="c-btn1 c-btn1-wht">
                <a href="#link<?php echo $faq['category']['id'] ?>">
                    <?php echo e($faq['category']['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>