<?php
if (isset($app)):
	$cms__queryParams = $app->getRequest()->query;
	$currentCategory = $cms__queryParams->getInt('category');
	$currentBrand = $cms__queryParams->getInt('brand');
	$currentYear = $cms__queryParams->getInt('year');
	$currentMonth = $cms__queryParams->getInt('month');
?>
    <div class="con_side">
        <div class="box_side box_cat">
            <p class="st accordion sp_only">
				<?php if(CURRENT_LOCALE === DEFAULT_LOCALE): ?>
                    <i>CATEGORY</i><span><?php echo e($view['translator']->trans('heading.categories', [], 'events')); ?></span>
				<?php else: ?>
                    <i><?php echo $view['translator']->trans('heading.categories', [], 'events'); ?></i>
				<?php endif; ?>
            </p>
            <ul>
                <li><a href="<?php echo LOCATION . 'events/'; ?>"<?php echo (empty($currentCategory) )? ' class="active"' : "" ?>><?php echo $view['translator']->trans('common.all_posts', [], 'events'); ?>（<?php echo $cms__categories['total']; ?>）</a></li>
				<?php foreach ($cms__categories['categories'] as $category): ?>
                    <li>
                        <a href="<?php echo LOCATION . 'events/?' . http_build_query(['category' => $category['entity']->getId()], null, '&amp;'); ?>"<?php echo (!empty($currentCategory) && $currentCategory == $category['entity']->getId())? ' class="active"' : "" ?>>
							<?php echo $category['entity']->getTitle(); ?>（<?php echo $category['count']; ?>）
                        </a>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>

		<?php
		if (!empty($cms__archives)):
			$showCnt = 0;
			?>
            <div class="box_side box_arc">
                <p class="st accordion sp_only">
					<?php if(CURRENT_LOCALE === DEFAULT_LOCALE): ?>
                        <i>archives</i><span><?php echo $view['translator']->trans('heading.archives', [], 'events'); ?></span>
					<?php else: ?>
                        <i><?php echo $view['translator']->trans('heading.archives', [], 'events'); ?></i>
					<?php endif; ?>
                </p>
                <ul>
					<?php
					foreach ($cms__archives as $year => $archive):
						$yearActive = ($currentYear == $year) ? ' active' : '';
						?>
                        <li><span class="accordion<?php echo ( !empty($currentYear) && $currentYear == $year )? ' active' : "" ?>"><?php echo $year; ?>（<?php echo $archive['total']; ?>）</span>
                            <ul<?php echo ( !empty($currentYear) && $currentYear == $year )? ' style="display:block"' : "" ?>>
								<?php
								foreach ($archive['month'] as $month => $cnt):
									$monthActive = ($currentYear . '-' . $currentMonth == $year . '-' . $month) ? ' active' : '';
									?>
                                    <li><a href="<?php echo LOCATION . 'events/' . '?' . http_build_query(['year' => $year, 'month' => $month], null, '&amp;'); ?>"<?php echo ( (!empty($currentYear) && !empty($currentMonth)) && ($currentYear == $year && $currentMonth == $month) )? ' class="active"' : "" ?>><?php echo $year; ?>/<?php echo $month; ?>（<?php echo $cnt['count']; ?>）</a></li>
								<?php endforeach; ?>
                            </ul>
                        </li>
					<?php endforeach; ?>
                </ul>
            </div>
		<?php endif; ?>
    </div>
<?php endif; ?>