
<div class="con_side">
	<div class="box_side box_cat">
		<p class="st accordion sp_only">
			<i>CATEGORY</i><span>カテゴリ</span>
		</p>
		<?php 
		if (isset($view)) {
			echo $view->partial('News/categories', ['cms__news_categories' => $cms__news_categories]);
		}
		?>
	</div><!-- /.box_side box_cat-->
	<?php if (false): ?>
	<div class="box_side box_arc">
		<p class="st accordion sp_only">
			<i>ARCHIVES</i><span>アーカイブ</span>
		</p>
		<ul>
			<li class=""><a href="https://demo.abi-cms.net/hotel/news/?year=2024&amp;month=8">2024/8（2）</a></li>
			<li class=""><a href="https://demo.abi-cms.net/hotel/news/?year=2024&amp;month=6">2024/6（6）</a></li>
		</ul>
	</div><!-- /.box_side -->
	<?php endif; ?>
</div><!-- /.con_side -->
