<?php if (!empty($cms__rooms_list)): ?>
    <ul class="con_link">
        <?php foreach ($cms__rooms_list as $room): ?>
            <li class="c-btn1 c-btn1-wht">
                <a href="#link_r<?php echo $room['id']; ?>">
                    <?php echo e($room['title']); ?> </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php foreach ($cms__rooms_list as $room): ?>
        <div class="con_rooms" id="link_r<?php echo $room['id']; ?>">
            <div class="box_rooms">
                <div class="box_st">
                    <h2 class="st c-jp_h2">
                        <?php echo e($room['title']); ?>
                    </h2>
                    <?php if ($room['smoking'] === '1'): ?>
                        <div class="box_smoke">
                            <p><i><img src="https://demo.abi-cms.net/hotel_full/_facility/rooms/images/ic_nosmoke.png" alt="禁煙ルーム"></i><em>禁煙ルーム</em></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box_slider">
                    <?php if (!empty($room['images'])): ?>
                        <div class="box_img slider_dots">
                            <?php foreach ($room['images'] as $key => $image): ?>
                                <div class="slide"><img src="<?php echo $image['file_url']; ?>" alt="<?php echo e((string)$image['alt_text']); ?>"></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="box_txt">
                        <p class="txt"><?php echo $room['text']; ?></p>
                        <?php if (!empty($room['360view_map_url'])): ?>
                            <p class="btn c-btn1"><a href="javascript:void(0);" data-src="#fancy_view_pop<?php echo $room['id']; ?>" data-fancybox="">360度ビュー</a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="box_detail">

                    <div class="wrp_detail">
                        <table class="tbl_room">
                            <tbody>
                                <?php if ((string)$room['detail_info']['bed'] !== ''): ?>
                                    <tr>
                                        <th>ベッド</th>
                                        <td><?php echo e($room['detail_info']['bed']); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ((string)$room['detail_info']['floor'] !== ''): ?>
                                    <tr>
                                        <th>客室面積</th>
                                        <td><?php echo e($room['detail_info']['floor']); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($room['detail_info']['other_table'])): ?>
                                    <?php foreach ($room['detail_info']['other_table']['items'] as $key => $item): ?>
                                        <tr>
                                            <th><?php echo e($item['row'][0]['value']); ?></th>
                                            <td><?php echo e($item['row'][1]['value']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($room['floor_image'])): ?>
                        <p class="romms_pop">
                            <a href="javascript:void(0);" data-src="<?php echo $room['floor_image']['file_url']; ?>" data-fancybox="">
                                <img src="<?php echo $room['floor_image']['file_url']; ?>" alt="<?php echo e($room['floor_image']['alt_text']); ?>">
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($room['plan_block'])): ?>
                    <ul class="box_btn">
                        <?php foreach ($room['plan_block'] as $key => $plan): ?>
                            <li>
                                <p class="smoke">
                                    <?php if ($plan['plan']['smoking'] === '1'): ?>
                                        <i><img src="https://demo.abi-cms.net/hotel_full/_facility/rooms/images/ic_nosmoke.png" alt="禁煙ルーム"></i>
                                    <?php endif; ?>
                                    <?php if ($plan['plan']['name'] !== ''): ?>
                                        <em><?php echo e($plan['plan']['name']); ?></em>
                                    <?php endif; ?>
                                </p>
                                <p class="btn c-btn1 c-btn1-rsv">
                                    <a href="<?php echo $plan['plan']['url']; ?>" target="_blank"><?php echo e($plan['plan']['notice']); ?></a>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>