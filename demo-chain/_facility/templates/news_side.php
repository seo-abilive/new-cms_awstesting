<div class="con_side">
	<div class="box_side box_cat">
		<p class="st accordion sp_only">
			<i>CATEGORY</i><span>カテゴリ</span>
		</p>
		<?php if (isset($view)) {
			echo $view->partial('News/categories', ['cms__news_categories' => $cms__news_categories, 'base_url' => LOCATION_FACILITY . 'news/']);
		} ?>
	</div>

</div>