<?php if (!empty($cms__news_list)) : ?>
    <?php foreach($cms__news_list as $news) : ?>
    <div class="box_news">
        <ul class="inn_news">
            <?php
            $link = $news['page_type'] === 'detail' ? LOCATION . "news/{$news['id']}/" : $news['page_url'];
            $target = $news['page_type'] === 'detail' ? '_self' : '_blank';
            ?>
            <li>
                <a href="<?php echo $link; ?>" target="<?php echo $target; ?>">
                    <div class="wrp_txt">
                        <div class="info">
                            <p class="dat"><?php echo (new \DateTime($news['public_date']))->format('Y.m.d') ?></p>
                            <?php if (!empty($news['categories'])) : ?>
                                <ul class="cat">
                                    <?php foreach($news['categories'] as $category) : ?>
                                        <li><?php echo $category['title']; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <p class="tit"><?php echo $news['title']; ?></p>
                        <span class="i"></span>
                    </div>
                    <?php if (!empty($news['thumbnail'])) : ?>
                        <p class="photo">
                            <img src="<?php echo $news['thumbnail']['file_url']; ?>" alt="<?php echo e($news['title']); ?>">
                        </p>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <p class="txt c-jp_b1">新着情報がありません</p>
<?php endif; ?>