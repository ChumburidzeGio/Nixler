@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<div class="container-fluid _m0 ng-cloak" ng-controller="StreamCtrl as vm" style="width: 100%">


	<script>window.stream = {!! $products->toJson() !!};</script>

	<div class="row">

		<div class="col-lg-2 col-md-2 col-xs-12">
			<div class="_tbs _mb15" id="categories-filter">

				<!--div class="_fs12 _ttu _pl15 _pb5">Categories</div-->

				<a href="{{ route('feed', request()->only(['price_min', 'price_max', 'query'])) }}" 
					class="_lim _hvrd _cg _brds3 _fs13{{ !request()->cat ? ' _hvrda' : '' }}">
					@if(request()->input('cat'))
					<i class="material-icons _fs18 _mr15 _va4">arrow_back</i> @lang('Go back')
					@else
					<i class="material-icons _fs18 _mr15 _va4">local_mall</i> @lang('All categories')
					@endif
				</a>
				
				@foreach($categories as $cat)
				<a href="{{ route('feed', 
				array_merge(request()->only(['price_min', 'price_max', 'query']), ['cat' => $cat->id])
				)}}"
				class="_lim _hvrd _cg _brds3 _telepsis _fs13{{ request()->cat == $cat->id ? ' _hvrda' : '' }}">
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

		@if(isset($facets) && $facets && $facets->count() > 0)

		<script>window.facets = {!! $facets->toJson() !!};</script>

		<div class="_db _tbs _ov _mb5" id="price-filter">
			<div class="_tb _crp _pl5" ng-if="vm.filters.price.avg">
				<span ng-click="vm.showPriceRange=!vm.showPriceRange" ng-class="{'_zi999': vm.showPriceRange}">
					<i class="material-icons _fs17 _va3 _mr10">filter_list</i>
					@lang('Price range')<i class="material-icons _fs17 _va5">expand_more</i>
				</span>

				<div class="_af _bgwt2 _zi999" ng-if="vm.showPriceRange" ng-click="vm.showPriceRange=0"></div>

				<div class="price-range _clear _mb15 _bgw _brds1 _z013 _p10 _posa _mt5 _w350px _zi999" ng-if="vm.showPriceRange" ng-init="vm.filters.price.min={{ request()->input('price_min', 0) }};vm.filters.price.max={{ request()->input('price_max', 9999) }};">
					<div class="_clear">
						<span class="_fs17 _clear">@{{ vm.filters.price.min | money }} - @{{ vm.filters.price.max | money }}</span>
						<span>@lang('The average price of product is') @{{ vm.filters.price.avg | money }}.</span>
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
						<input name="cat" type="hidden" value="{{ request()->input('cat') }}">
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

	@include('products.index')

</div>



</div>
</div>

@endsection
