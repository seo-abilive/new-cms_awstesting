<?php
// $pageStatusList = $view['custom_helper']->getPageStatusList();
$pageStatusList = [];
?>
<header id="header">
    <?php if ($phone) : ?>
        <div class="con_app view_sp js-closeBox">
            <i class="ic-clearclose btn js-closeBtn"></i>
            <div class="box_txt">
                <?php if ($android) : ?>
                    <p class="img"><img src="<?php echo LOCATION_file; ?>images/common/img_icon.png" alt="DEMO HOTELS"></p>
                    <p class="txt"><?php echo e($view['translator']->trans('header.app_text', [], 'common')); ?></p>
                    <p class="btn"><a target="_blank" href="<?php echo LOCATION_googleplay; ?>"><?php echo e($view['translator']->trans('header.app_download', [], 'common')); ?></a></p>
                <?php else : ?>
                    <p class="img"><img src="<?php echo LOCATION_file; ?>images/common/img_icon.png" alt="DEMO HOTELS"></p>
                    <p class="txt"><?php echo e($view['translator']->trans('header.app_text', [], 'common')); ?></p>
                    <p class="btn"><a target="_blank" href="<?php echo LOCATION_appstore; ?>"><?php echo e($view['translator']->trans('header.app_download', [], 'common')); ?></a></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    if (isset($view)) {
        echo $view->partial('Common/emergency_news');
    }
    ?>

    <div class="con_header">
        <p class="logo">
            <a href="<?php echo LOCATION; ?>">
                <img class="icon" src="<?php echo LOCATION_FILE; ?>images/common/logo-151x32.png" alt="">
            </a>
        </p>
        <nav class="box_nav view_pc-tab">
            <ul id="gnav">
                <li><a href="<?php echo LOCATION; ?>">HOME</a></li>
                <li><a href="<?php echo LOCATION; ?>hotel/">ホテル一覧</a></li>
                <li><a href="<?php echo LOCATION; ?>news/">お知らせ</a></li>
                <li><a href="<?php echo LOCATION; ?>faq/">よくあるご質問</a></li>

            </ul>
            <div class="rsv js-search-btn"><span>空室検索</span></div>
        </nav>
    </div>
    <div class="con_fix"></div>
</header><!-- /#header -->