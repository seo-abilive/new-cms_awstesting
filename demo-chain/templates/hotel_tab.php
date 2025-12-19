
		<h3>ホテルを探す</h3>
		<div class="box_tab view_pc">
			<div class="area area_main">
				<p class="st">主要都市</p>
				<ul>
					<li<?php if($page === 'hotel') echo ' class="active"' ?>><a href="<?php echo LOCATION_general; ?>hotel/">東京都</a></li>
					<li<?php if($page === 'hotel_area1') echo ' class="active"' ?>><a href="<?php echo LOCATION_general; ?>hotel/area1.php">神奈川県</a></li>
					<li<?php if($page === 'hotel_area2') echo ' class="active"' ?>><a href="<?php echo LOCATION_general; ?>hotel/area2.php">大阪府</a></li>
					<li<?php if($page === 'hotel_area3') echo ' class="active"' ?>><a href="<?php echo LOCATION_general; ?>hotel/area3.php">京都府</a></li>
				</ul>
			</div>
			<div class="area area_sub">
				<p class="st">エリア別</p>
				<ul>
					<li<?php if($page === 'hotel_area4') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area4.php">北海道・東北</a></li>
					<li<?php if($page === 'hotel_area5') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area5.php">関東</a></li>
					<li<?php if($page === 'hotel_area6') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area6.php">中部</a></li>
					<li<?php if($page === 'hotel_area7') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area7.php">近畿</a></li>
					<li<?php if($page === 'hotel_area8') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area8.php">中国・四国</a></li>
					<li<?php if($page === 'hotel_area9') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area9.php">九州</a></li>
					<li<?php if($page === 'hotel_area10') echo ' class="active"'; ?>><a href="<?php echo LOCATION_general; ?>hotel/area10.php">海外</a></li>
				</ul>
			</div>
		</div>
		<div class="box_tab view_tab-sp">
			<ul class="tab" id="js-tab">
				<li class="area_main">主要都市</li>
				<li class="area_sub">エリア別</li>
			</ul>
			<div class="panel">
				<div class="link area_main hide">
					<div class="js-scroll" data-mcs-axis="x">
						<div class="item<?php if($page === 'hotel') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/">東京都</a></div>
						<div class="item<?php if($page === 'hotel_area1') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area1.php">神奈川県</a></div>
						<div class="item<?php if($page === 'hotel_area2') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area2.php">大阪府</a></div>
						<div class="item<?php if($page === 'hotel_area3') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area3.php">京都府</a></div>
					</div>
				</div>
				<div class="link area_sub hide">
					<p class="icon js-scr_Icon">
						<span class="view_pc-tab">scrollable</span>
						<i><img src="<?php echo LOCATION; ?>hotel/images/ic_scroll.png" alt="scrollable"></i>
					</p>
					<div class="js-scroll" data-mcs-axis="x">
						<div class="item<?php if($page === 'hotel_area4') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area4.php">北海道・東北</a></div>
						<div class="item<?php if($page === 'hotel_area5') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area5.php">関東</a></div>
						<div class="item<?php if($page === 'hotel_area6') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area6.php">中部</a></div>
						<div class="item<?php if($page === 'hotel_area7') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area7.php">近畿</a></div>
						<div class="item<?php if($page === 'hotel_area8') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area8.php">中国・四国</a></div>
						<div class="item<?php if($page === 'hotel_area9') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area9.php">九州</a></div>
						<div class="item<?php if($page === 'hotel_area10') echo ' active'; ?>"><a href="<?php echo LOCATION_general; ?>hotel/area10.php">海外</a></div>
					</div>
				</div>
			</div>
		</div>
		<div class="box_sort">
			<p class="st"><span><?php echo $areaName; ?>のホテル : 00施設</span></p>
			<?php if($page === 'hotel_area4') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">北海道</a></li>
				<li><a href="#zzz">青森県</a></li>
				<li><a href="#zzz">岩手県</a></li>
				<li><a href="#zzz">福島県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area5') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">東京都</a></li>
				<li><a href="#zzz">神奈川県</a></li>
				<li><a href="#zzz">埼玉県</a></li>
				<li><a href="#zzz">千葉県</a></li>
				<li><a href="#zzz">栃木県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area6') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">新潟県</a></li>
				<li><a href="#zzz">愛知県</a></li>
				<li><a href="#zzz">長野県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area7') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">京都府</a></li>
				<li><a href="#zzz">大阪府</a></li>
				<li><a href="#zzz">兵庫県</a></li>
				<li><a href="#zzz">滋賀県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area8') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">広島県</a></li>
				<li><a href="#zzz">山口県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area9') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">熊本県</a></li>
			</ul>
			<?php elseif($page === 'hotel_area10') : ?>
			<ul class="txt">
				<li class="active"><a href="#zzz">全て</a></li>
				<li><a href="#zzz">韓国</a></li>
				<li><a href="#zzz">台湾</a></li>
			</ul>
			<?php endif ?>
			<p class="btn"><a id="js-modal" class="js-modal" href="#modal_map">地図を見る</a></p>
			<div class="wrp_sort">
				<p class="st accordion">こだわり条件からホテルを探す<i></i></p>
				<div class="inner">
					<p class="sst"><i><img src="./images/ic_sort.png" alt=""></i>施設・設備から絞り込む</p>
					<ul>
						<li><label><input type="checkbox">朝食</label></li>
						<li><label><input type="checkbox">カフェ・レストラン・バー</label></li>
						<li><label><input type="checkbox">駐車場</label></li>
						<li><label><input type="checkbox">宴会場・会議室</label></li>
						<li><label><input type="checkbox">全室禁煙</label></li>
						<li><label><input type="checkbox">喫煙ルーム</label></li>
						<li><label><input type="checkbox">全館浄水システム</label></li>
					</ul>
					<p class="sst"><i><img src="./images/ic_sort.png" alt=""></i>ブランドから絞り込む</p>
					<ul>
						<li><label><input type="checkbox">サンプルフレッサイン</label></li>
						<li><label><input type="checkbox">ホテルサンルート</label></li>
						<li><label><input type="checkbox">ザ・スプラジール</label></li>
						<li><label><input type="checkbox">ザ・DEMOホテル</label></li>
					</ul>
					<p class="btn_sort c-btn1"><span class="over">この条件で絞り込む</span></p>
				</div>
			</div>
		</div>

		<div id="modal_map">
			<?php if($page === 'hotel') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=16LyZwPnaRh0Tm5IH7mICI6ztDBOkjsD3"></iframe>
			<?php elseif($page === 'hotel_area1') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1kBhCyqMfn9adfQdjmLmvpTNczDVk8IHI"></iframe>
			<?php elseif($page === 'hotel_area2') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1mIY2AparsINL1AsTL3QNll20MSC9hcsN"></iframe>
			<?php elseif($page === 'hotel_area3') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1EJIQqcsNJ8BEsZbIe9zQ4Wz57ekgpRTn"></iframe>
			<?php elseif($page === 'hotel_area4') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1wXvgnkO06W1VuRuVVfOkQZn3FNEpxNSF"></iframe>
			<?php elseif($page === 'hotel_area5') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1nv4aHt9yegR2-eik62uoFf1vKHP1rMvS"></iframe>
			<?php elseif($page === 'hotel_area6') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1y8hBdNxR_22urYlGZe1cusKif3U5D95a"></iframe>
			<?php elseif($page === 'hotel_area7') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1zfBSnUNQvZmTN5P9V4InN4iVqfTwUaCd"></iframe>
			<?php elseif($page === 'hotel_area8') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1dJ4sQPUgc94rhuT8Uj71aBEKhwbQ_cVA"></iframe>
			<?php elseif($page === 'hotel_area9') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1frGsVcvw-YnWAlQZIueo-pLwEfsvuSKt"></iframe>
			<?php elseif($page === 'hotel_area10') : ?>
				<iframe src="https://www.google.com/maps/d/u/0/embed?mid=14Qx_L6XPb6JTRXGIeEU_tFuVVPX7ehws"></iframe>
			<?php endif ?>
		</div>