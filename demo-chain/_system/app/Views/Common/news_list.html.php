<?php if (!empty($cms__news_list)) : ?>
    <?php $base_url = $base_url ?? LOCATION; ?>

    <div class="box_news">
        <ul class="inn_news">
            <?php foreach ($cms__news_list as $news) : ?>
                <?php
                switch ($news['page_type']) {
                    case 'url':
                        $link = $news['page_url'];
                        break;
                    case 'pdf':
                        $link = $news['pdf']['file_url'];
                        break;
                    default:
                        $link = $base_url . "news/{$news['id']}/";
                        break;
                }
                $target = $news['page_type'] === 'detail' ? '_self' : '_blank';
                ?>
                <li>
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>">
                        <div class="wrp_txt">
                            <div class="info">
                                <p class="dat"><?php echo (new \DateTime($news['public_date']))->format('Y.m.d') ?></p>
                                <?php if (!empty($news['categories'])) : ?>
                                    <ul class="cat">
                                        <?php foreach ($news['categories'] as $category) : ?>
                                            <li><?php echo $category['title']; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <p class="tit"><?php echo $news['title']; ?></p>
                            <span class="i"></span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php else : ?>
    <p class="txt c-jp_b1">新着情報がありません</p>
<?php endif; ?>