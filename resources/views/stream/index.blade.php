@extends('layouts.app')

@section('content')

<div class="container-fluid _m0 ng-cloak" ng-controller="StreamCtrl as vm" style="width: 100%">


	<script>window.stream = {!! $products->toJson() !!};</script>

	<div class="row">

		<div class="col-sm-2 hidden-xs hidden-sm">
			<div class="">

				<!--div class="_fs12 _ttu _pl15 _pb5">Categories</div-->

				@if(request()->input('cat'))
				<a href="{{ request()->has('query') ? route('feed', ['query' => request()->input('query')]) : route('feed') }}" 
					class="_lim _hvrd _cg _brds3 _fs13{{ !request()->cat ? ' _hvrda' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">arrow_back</i> @lang('Go back')
				</a>
				@else
				<a href="{{ request()->has('query') ? route('feed', ['query' => request()->input('query')]) : route('feed') }}" 
					class="_lim _hvrd _cg _brds3 _fs13{{ !request()->cat ? ' _hvrda' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">local_mall</i> @lang('All categories')
				</a>
				@endif

				@foreach($categories as $cat)
				<a href="{{ request()->has('query') ? route('feed', ['query' => request()->input('query'), 'cat' => $cat->id]) : route('feed', ['cat' => $cat->id]) }}"
					class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == $cat->id ? ' _hvrda' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">{{ $cat->icon or 'brightness_1' }}</i> {{ $cat->name }}
				</a>
				@endforeach


				{{--<div class="_fs12 _ttu _pl15 _pt10 _pb5">Order</div>
				<a href="{{ url('/new-product') }}" class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == 1 ? ' _hvra' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">grade</i> Relevance
				</a>
				<a href="{{ url('/new-product') }}" class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == 1 ? ' _hvra' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">fiber_new</i> Newest arrivals
				</a>
				<a href="{{ url('/new-product') }}" class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == 1 ? ' _hvra' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">attach_money</i> Price: Low to high
				</a>
				<a href="{{ url('/new-product') }}" class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == 1 ? ' _hvra' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">attach_money</i> Price: High to low
				</a>--}}

			</div>
		</div>
		<div class="col-lg-9 col-md-10 col-xs-12">

		@if(request()->has('query') && $facets->count() > 0)

			<script>window.facets = {!! $facets->toJson() !!};</script>

			<div class="_db _tbs _ov">
				<div class="_tb _crp">
					<span ng-click="vm.showPriceRange=!vm.showPriceRange" ng-class="{'_zi999': vm.showPriceRange}">
						@lang('Price range')<i class="material-icons _fs17 _va5">expand_more</i>
					</span>

					<div class="_af _bgwt2 _zi999" ng-if="vm.showPriceRange" ng-click="vm.showPriceRange=0"></div>

					<div class="price-range _clear _mb15 _bgw _brds1 _z013 _p10 _posa _mt5 _w350px _zi999" ng-if="vm.showPriceRange" ng-init="vm.filters.price.min={{ request()->input('price_min') }};vm.filters.price.max={{ request()->input('price_max') }};">
						<div class="_clear">
							<span class="_fs17 _clear">@{{ vm.filters.price.min }}zł - @{{ vm.filters.price.max }}zł</span>
							<span class="">@lang('The average price of product is') @{{ vm.filters.price.avg }}zł.</span>
						</div>

						<rzslider
						rz-slider-tpl-url="rzslidera.html"
					    rz-slider-model="vm.filters.price.min"
					    rz-slider-high="vm.filters.price.max"
					    rz-slider-options="vm.priceSliderOptions"></rzslider>

						<script type="text/ng-template" id="rzslidera.html">
							<div class="rzslider">
								<span class="rz-bar-wrapper"><span class="rz-bar"></span></span>
								<span class="rz-bar-wrapper">
									<span class="rz-bar rz-selection" ng-style="barStyle"></span>
								</span>
								<span class="rz-pointer rz-pointer-min" ng-style=minPointerStyle></span>
								<span class="rz-pointer rz-pointer-max" ng-style=maxPointerStyle></span>
								<span class="rz-bubble rz-limit rz-floor"></span>
								<span class="rz-bubble rz-limit rz-ceil"></span>
								<span class="rz-bubble"></span>
								<span class="rz-bubble"></span>
								<span class="rz-bubble"></span>
								<ul ng-show="showTicks" class="rz-ticks">
									<li ng-repeat="t in ticks track by $index" class="rz-tick"
									ng-class="{'rz-selected': t.selected}" ng-style="t.style">
									<span ng-if="t.value != null" class="rz-tick-value" style="height:@{{t.legend}}px"></span>
								</li>
							</ul>
						   </div>
					   </script>

					   <div class="_db _tbs _ov _mt5">
							<div class="_tb _crp _pl5 _fs15 _pb0" ng-click="vm.showPriceRange=0">@lang('Cancel')</div>
							<div class="_tb _crp _c4 _right _pr5 _fs15 _pb0" onclick="event.preventDefault();document.getElementById('search-filters-form').submit();">@lang('Apply')</div>
							<form id="search-filters-form" action="{{ route('feed') }}">
								<input name="query" type="hidden" value="{{ request()->input('query') }}">
								<input name="price_min" type="hidden" ng-value="vm.filters.price.min">
								<input name="price_max" type="hidden" ng-value="vm.filters.price.max">
							</form>
						</div>

						

					</div>
				</div>
			</div>

		@endif

		@if(auth()->guest() && !request()->has('cat') && !request()->has('query'))
		<a class="_cw _clear _p10 _mb15 _brds3 _bgbl _tac _thvrw" href="{{ route('register') }}">
			<i class="material-icons _left _mr15 _fs24 _ml5">android</i>
			<span class="_oh _fs15">@lang('Our Artificial Intelligence knows what you want... Would you like to check?')</span>
			<span class="_right">
				@lang('Register now') <i class="material-icons _mr5 _fs24 _ml5 _va7">arrow_forward</i>
			</span>
		</a>
		@endif

		@if(isset($users) && $users->count())
		<span class="_fs16 _clear _mb15">@lang('Accounts')</span>
		<div class="row _mb15"> 
			@foreach($users as $user)
			<a href="{{ $user->link() }}" class="_left col-xs-2 _tac _oh">
				<img src="{{ $user->avatar('aside') }}" class="_brds3">
				<span class="_telipsis _mt10 _clear">{{ $user->name }}</span>
			</a>
			@endforeach
		</div>
		<span class="_mt30 _fs16 _clear _mb15">@lang('Products')</span>
		@endif

		<div class="row _mb15" ng-if="vm.stream.data.length">

			<div class="col-lg-3 col-sm-4 col-xs-6 _cxxs12 _pb15" ng-repeat="product in vm.stream.data">
				<a class="_bgw _b1 _brds3 _clear" href="@{{ product.url }}">

					<img ng-src="@{{ product.photo }}" class="_db _w100">

					<div class="_pl15 _pr15 _pt10 _pb10">
						<span class="_cb _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs13">
							@{{ product.title }}
						</span>
						<span class="_cg _clear _fs12  _telipsis _w100 _oh _pr10">
							<b class="_ci">@{{ product.price }}</b> · @{{ product.owner }} · <i class="material-icons _fs13 _va2 _cg">favorite</i> @{{ product.likes_count }}
						</span>
					</div>

				</a>
			</div>

		</div>

		<div class="_tbs _tac _bg5 _mt15 _crp _clear" ng-click="vm.load()" ng-if="vm.isMore()">
			<span class="_tb">
				@lang('More products')
			</span>
		</div>

		<div class="_tac _pt15 _mt70 _c3" ng-if="!vm.stream.data.length">
			<h5 class="_fw400">@lang('There is no products to show.')</h5>
		</div>

	</div>



</div>
</div>

@endsection
