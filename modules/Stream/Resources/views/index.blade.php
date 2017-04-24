@extends('layouts.app')

@section('content')

<div class="container-fluid _m0 ng-cloak" ng-controller="StreamCtrl as vm" style="width: 100%">

	<script>window.stream = {!! $products->toJson() !!};</script>

	<div class="row">

		<div class="col-sm-2 hidden-xs hidden-sm">
			<div class="_posf">

				<!--div class="_fs12 _ttu _pl15 _pb5">Categories</div-->

				<a href="{{ request()->has('query') ? route('feed', ['query' => request()->input('query')]) : route('feed') }}" 
					class="_lim _hvrd _cg _brds3 _fs13{{ !request()->cat ? ' _hvrda' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">local_mall</i> All categories
				</a>
				@foreach($categories as $id => $cat)
				<a href="{{ request()->has('query') ? route('feed', ['query' => request()->input('query'), 'cat' => $id]) : route('feed', ['cat' => $id]) }}"
					class="_lim _hvrd _cg _brds3 _fs13{{ request()->cat == $id ? ' _hvrda' : '' }}">
					<i class="material-icons _fs18 _mr15 _va4">{{ $cat['icon'] }}</i> {{ $cat['name'] }}
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

			@if($products->getResource()->getData()->total())
			<div class="row _mb15">

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
					More products
				</span>
			</div>
			@else

			<div class="_tac _pt15 _mt70 _c3">
				<h5 class="_fw400">There is no products to show.</h5>
			</div>

			@endif

		</div>



	</div>
</div>

@endsection
