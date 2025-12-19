<div class="con_tab">
    <div class="area">
        <?php if (isset($view)) {
            echo $view->partial('Faq/tab', ['cms__faq' => $cms__faq]);
        } ?>
    </div>
</div>