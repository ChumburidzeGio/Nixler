
	{{-- <div class="_z013 _bgw _brds2 _mb15 _clear _mt15">

		
		<span class="_fs13 _clear _li _bb1 _cb">
			@lang('Analytics')
		</span>

			@if($analytics['views'])
			<div class="_clear _br1 _pb15">

				<div class="_ml15 _mt15 _fs15 _pl5 _c2">Views</div>
				<div id="views"></div>

				<?= Lava::render('AreaChart', 'views', 'views') ?>

			</div>
			@endif

			@if($analytics['avgSession'])
			<div class="_clear _br1 _pb15 _bt1">

				<div class="_ml15 _mt15 _fs15 _pl5 _c2">Average Session (in Seconds)</div>
				<div id="pop_div"></div>

				<?= Lava::render('AreaChart', 'avgsession', 'pop_div') ?>

			</div>
			@endif

			@if($analytics['bounceRate'])
			<div class="_clear _br1 _pb10 _bt1">

				<div class="_ml15 _mt15 _fs15 _pl5 _c2">Bounce Rate</div>
				<div id="bounceRate"></div>

				<?= Lava::render('AreaChart', 'bounceRate', 'bounceRate') ?>

				<p class="_ml15 _mt15 _fs13 _pl5 _c2 _mr15">*How big perchent of users leave your product immediatelly. Lower better.</p>
			</div>
			@endif

			@if(!$analytics['views'] and !$analytics['avgSession'] and !$analytics['bounceRate'])
				<p class="_pl15 _pt10">@lang('We don\'t have enought data to show analytics.')</p>
			@endif

	</div> --}}