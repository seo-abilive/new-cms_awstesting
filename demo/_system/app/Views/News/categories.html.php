<ul>
    <li>
        <a href="./" class="<?php echo $cms__news_categories['active'] === null ? 'active' : ''; ?>">すべての記事（<?php echo $cms__news_categories['all']; ?>）</a>
    </li>
    <?php foreach ($cms__news_categories['items'] as $category) : ?>
        <li>
            <a href="./?category=<?php echo $category['id'] ?>" class="<?php echo $cms__news_categories['active'] == $category['id'] ? 'active' : ''; ?>">
                <?php echo e($category['title']) ?>（<?php echo $category['contents_count']; ?>）
            </a>
        </li>
    <?php endforeach; ?>
</ul>
