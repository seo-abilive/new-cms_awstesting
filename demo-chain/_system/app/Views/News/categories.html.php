<?php
$base_url = $base_url ?? LOCATION . 'news/';
?>
<ul>
    <li>
        <a href="<?php echo $base_url; ?>" class="<?php echo $cms__news_categories['active'] === null ? 'active' : ''; ?>">すべての記事（<?php echo $cms__news_categories['all']; ?>）</a>
    </li>
    <?php foreach ($cms__news_categories['items'] as $category) : ?>
        <li>
            <a href="<?php echo $base_url; ?>?category=<?php echo $category['id'] ?>" class="<?php echo $cms__news_categories['active'] == $category['id'] ? 'active' : ''; ?>">
                <?php echo e($category['title']) ?>（<?php echo $category['contents_count']; ?>）
            </a>
        </li>
    <?php endforeach; ?>
</ul>