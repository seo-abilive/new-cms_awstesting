<?php if ($cms__pagination): ?>
    <div class="box_pager">
        <ul>
            <?php if ($cms__pagination['prev_url']): ?>
                <li class="back">
                    <a href="<?php echo $cms__pagination['prev_url'] ?>">
                        <span class="r_arrow">前へ</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php foreach ($cms__pagination['page_numbers'] as $page): ?>
                <li class="<?php echo $page['is_current'] ? 'active' : '' ?>">
                    <a href="<?php echo $page['url'] ?>"><?php echo $page['number'] ?></a>
                </li>
            <?php endforeach; ?>
            <?php if ($cms__pagination['next_url']): ?>
                <li class="next">
                    <a href="<?php echo $cms__pagination['next_url'] ?>">
                        <span class="r_arrow">次へ</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>