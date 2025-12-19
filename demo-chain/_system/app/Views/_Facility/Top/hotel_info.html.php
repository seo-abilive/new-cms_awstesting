<div class="box_map">
    <?php if ($cms__facility_setting['thumbnail']) : ?>
        <p class="photo">
            <img src="<?php echo $cms__facility_setting['thumbnail']['file_url']; ?>" alt="<?php echo e($cms__facility_setting['thumbnail']['alt_text']); ?>">
        </p>
    <?php endif; ?>
    <?php if ($cms__facility_setting['iframe_map']) : ?>
        <div class="wrp_map view_pc-tab">
            <div class="inner">
                <?php echo $cms__facility_setting['iframe_map']; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="box_table">
    <h3 class="st c-jp_h3"><?php echo e($cms__facility_setting['short_name']); ?></h3>
    <table class="c-table sp_block">
        <tbody>
            <tr>
                <th>住所</th>
                <td>
                    <em>
                        〒<?php echo e($cms__facility_setting['zip-code']); ?> <?php echo e($cms__facility_setting['address']); ?> </em>
                </td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>TEL：<span class="tel"><?php echo e($cms__facility_setting['tel']); ?></span></td>
            </tr>
            <?php foreach ($cms__top_page_setting['hotel_info'] as $hotelInfo): ?>
                <tr>
                    <th><?php echo e($hotelInfo['column']['th']); ?></th>
                    <td><?php echo $hotelInfo['column']['td']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>