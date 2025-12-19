<?php if (!empty($cms__hotel_list)) : ?>
    <ul>
        <?php foreach ($cms__hotel_list as $hotel) : ?>
            <?php
            $link = LOCATION . $hotel['assignable']['alias'];
            ?>
            <li class="">
                <div class="wrp_img">
                    <?php if (!empty($hotel['thumbnail'])) : ?>
                        <a href="<?php echo $link; ?>">
                            <img src="<?php echo $hotel['thumbnail']['file_url']; ?>" alt="<?php echo e($hotel['title']); ?>">
                        </a>
                    <?php endif; ?>
                </div>
                <div class="wrp_txt">
                    <p class="st">
                        <a href="<?php echo $link; ?>">
                            <?php echo e($hotel['name']); ?> </a>
                    </p>
                    <address>
                        <span class="view_pc-tab">
                            <?php echo e($hotel['zip-code']); ?>
                        </span>
                        <?php echo e($hotel['address']); ?>
                    </address>
                    <div class="btns">

                        <p class="txt_tel view_pc-tab">
                            <span><i><?php echo e($hotel['tel']); ?></i></span>
                        </p>

                        <p class="txt_tel view_sp">
                            <span class="tel"><i class="view_pc-tab"><?php echo e($hotel['tel']); ?></i></span>
                        </p>

                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>