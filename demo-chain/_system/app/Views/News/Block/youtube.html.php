<?php if (!empty($block) && !empty($block['url'])): ?>
    <?php
    /**
     * 入力されたYouTubeのURLから動画ID（コード）を抽出する関数
     */
    function getYouTubeId($url)
    {
        // パターン1: https://www.youtube.com/watch?v=VIDEOID
        if (preg_match('#(?:\?|&)v=([0-9A-Za-z_-]{11})#', $url, $matches)) {
            return $matches[1];
        }
        // パターン2: https://youtu.be/VIDEOID
        if (preg_match('#youtu\.be/([0-9A-Za-z_-]{11})#', $url, $matches)) {
            return $matches[1];
        }
        // パターン3: https://www.youtube.com/embed/VIDEOID
        if (preg_match('#youtube\.com/embed/([0-9A-Za-z_-]{11})#', $url, $matches)) {
            return $matches[1];
        }
        return '';
    }

    $youtubeId = getYouTubeId($block['url']);
    ?>
    <!-- YOUTUBE -->
    <div class="box_news_parts">
        <div class="b_youtube_l">
            <div class="iframe_res">
                <?php if ($youtubeId): ?>
                    <iframe width="1200" height="679" src="https://www.youtube.com/embed/<?= htmlspecialchars($youtubeId, ENT_QUOTES, 'UTF-8') ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                <?php endif; ?>
            </div>
            <?php if (!empty($block['description'])): ?>
                <p class="txt_caption"><?php echo e($block['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>