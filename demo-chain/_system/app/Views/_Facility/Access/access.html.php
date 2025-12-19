<?php if (!empty($cms__access)) : ?>
    <div class="con_map">
        <?php if ($cms__access['iframe_map_url'] !== '') : ?>
            <div class="box_map">
                <?php echo $cms__access['iframe_map_url'] ?>
            </div>
        <?php endif; ?>
        <div class="box_detail">
            <h2 class="st"><?php echo e($cms__facility_setting['short_name']); ?></h2>
            <table class="tbl_access">
                <tbody>
                    <tr>
                        <th>住所</th>
                        <td class="box_s">
                            <em class="add"><?php echo e($cms__facility_setting['zip-code'] . ' ' . $cms__facility_setting['address']); ?></em>
                        </td>
                    </tr>
                    <tr>
                        <th>TEL</th>
                        <td>TEL：<i><span class="tel"><?php echo e($cms__facility_setting['tel']); ?></span></i> <i class="line"></td>
                    </tr>
                    <?php if (!empty($cms__access['intro_table'])) : ?>
                        <?php foreach ($cms__access['intro_table']['items'] as $intro) : ?>
                            <tr>
                                <th><?php echo e($intro['row'][0]['value']); ?></th>
                                <td><?php echo $intro['row'][0]['value']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <ul class="box_btn">
                <?php if ($cms__access['large_btn']['url'] !== '') : ?>
                    <li class="btn c-btn1 c-btn1-wht">
                        <a href="<?php echo e($cms__access['large_btn']['url']); ?>" target="_blank"><?php echo e($cms__access['large_btn']['btn_title'] ?? '広域地図（PDF）を見る'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($cms__access['narrow_btn']['url'] !== '') : ?>
                    <li class="btn c-btn1 c-btn1-wht">
                        <a href="<?php echo e($cms__access['narrow_btn']['url']); ?>" target="_blank"><?php echo e($cms__access['narrow_btn']['btn_title'] ?? '狭域地図（PDF）を見る'); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <div class="con_route">
        <div class="box_route">

            <div class="wrp_route" id="link01">
                <?php if ((bool)$cms__access['train_access']['is_show']) : ?>
                    <h2 class="st">電車でお越しの方へ</h2>
                    <div class="inner">
                        <?php if (!empty($cms__access['train_access']['list'])) : ?>
                            <ul class="route">
                                <?php foreach ($cms__access['train_access']['list'] as $train) : ?>
                                    <li><em><?php echo e($train['value']); ?></em></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <?php if ($cms__access['train_access']['notice'] !== '') : ?>
                        <p class="note"><?php echo e($cms__access['train_access']['notice']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($cms__access['train_access']['image'])) : ?>
                        <p class="btn_pop c-btn1 c-btn1-wht"><a href="<?php echo e($cms__access['train_access']['image']['file_url']); ?>" data-fancybox="">路線図を見る</a></p>
                    <?php endif; ?>

                    <?php if (!empty($cms__access['station_access_block'])) : ?>

                        <div class="box_sta" id="lnk_station">
                            <h3 class="st_sta">駅からの詳しい行き方（写真付き）</h3>
                            <?php foreach ($cms__access['station_access_block'] as $station) : ?>
                                <div class="box_sta_inner">
                                    <p class="st accordion active"><?php echo e($station['station_access']['title']); ?></p>
                                    <ul class="box_route_map active">
                                        <?php foreach ($station['station_access']['images'] as $key => $item) : ?>
                                            <li>
                                                <div class="box_photo">
                                                    <p class="number"><?php echo sprintf('%02d', $key + 1); ?></p>
                                                    <p class="photo"><img src="<?php echo e($item['file_url']); ?>" alt="<?php echo e($item['alt_text']); ?>"></p>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($cms__access['sightseeing_group'])) : ?>
                    <div class="box_sta">
                        <h3 class="st_sta">周辺観光のご案内</h3>
                        <ul class="box_route_info">
                            <?php foreach ($cms__access['sightseeing_group'] as $sightseeing) : ?>
                                <li>
                                    <p class="info_sst accordion sp_only" style="height: 25.3516px;"><?php echo e($sightseeing['item']['th']); ?></p>
                                    <div class="inner" style="height: 108.984px;">
                                        <div class="txt"><?php echo e($sightseeing['item']['td']); ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>