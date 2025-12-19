<?php if (!empty($block) && !empty($block['items'])): ?>
    <?php $colmns = $block['columns'] ?? 1; ?>
    <!-- テーブル -->
    <div class="box_news_parts">
        <table class="c-table">
            <tbody>
                <?php foreach ($block['items'] as $item): ?>
                    <tr>
                        <?php for ($i = 0; $i < (int)$colmns; $i++): ?>
                            <?php
                            $value = !empty($item['row'][$i]['value']) ? $item['row'][$i]['value'] : '';
                            $tag = !empty($item['row'][$i]['is_head']) && $item['row'][$i]['is_head'] ? 'th' : 'td';
                            ?>
                            <<?php echo $tag ?>><?php echo nl2br(e($value)) ?></<?php echo $tag ?>>
                        <?php endfor ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>