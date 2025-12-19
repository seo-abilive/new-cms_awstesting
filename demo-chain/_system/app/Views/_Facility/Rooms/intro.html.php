<?php if (!empty($cms__rooms_info)): ?>
    <div class="con_int">
        <div class="box_txt">
            <?php if ((string)$cms__rooms_info['intro_heading'] !== ''): ?>
                <h2 class="c-jp_h2"><?php echo e($cms__rooms_info['intro_heading']); ?></h2>
            <?php endif; ?>
            <?php if ((string)$cms__rooms_info['text'] !== ''): ?>
                <p class="txt c-jp_b1"><?php echo $cms__rooms_info['text']; ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($cms__rooms_info['image'])): ?>
            <p class="photo">
                <img src="<?php echo $cms__rooms_info['image']['file_url']; ?>" alt="<?php echo e($cms__rooms_info['image']['alt_text']); ?>">
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>