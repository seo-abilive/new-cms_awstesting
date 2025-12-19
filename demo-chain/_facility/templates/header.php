<header id="header">
    <?php
    if (isset($view)) {
        echo $view->partial('Common/emergency_news');
    }
    ?>

    <div class="con_header">
        <p class="logo"><a href="<?php echo LOCATION_FACILITY; ?>">
                <em><?php echo e($cms__facility_setting['name']); ?></em>
            </a></p>
        <nav class="box_nav view_pc-tab">
            <ul id="gnav">
                <li><a href="<?php echo LOCATION_FACILITY; ?>">HOME</a></li>
                <li><a href="<?php echo LOCATION_FACILITY; ?>rooms/">客室</a></li>
                <li><a href="<?php echo LOCATION_FACILITY; ?>access/">アクセス</a></li>
                <li><a href="<?php echo LOCATION_FACILITY; ?>faq/">よくあるご質問</a></li>
            </ul>
            <div class="rsv js-search-btn"><span>空室検索</span></div>
            <div class="menu js-btn_menu"><span class="c-en_capb">MENU</span></div>
        </nav>
    </div>
    <div class="con_fix"></div>
</header><!-- /#header -->