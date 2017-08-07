@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<div class="container-fluid _m0 ng-cloak" ng-controller="StreamCtrl as vm" style="width: 100%">


	<script>window.stream = {!! $collections->toJson() !!};</script>

	<div class="row">

		<div class="col-lg-9 col-md-10 col-xs-12">

			<div id="products">

				<div class="row _mb15" ng-if="vm.stream.items.length">

					<div class="_c2 _fs15 _mb10 _ml15">
						მოდა
					</div>

					<div class="col-lg-3 col-sm-4 col-xs-6 _cxxs12 _pb15 _mb10 _crp" ng-repeat="product in vm.stream.items">
						<a class="_clear _brds3 _hvrcard _posr" ng-href="@{{ product.url }}">

							<img ng-src="@{{ product.photo }}" class="_db _w100">

							<div class="_pl10 _pr15 _pt10 _pb10 _a0 _zi9">
								<span class="_cb _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18 _cw" ng-bind="product.name"></span>
							</div>

							<span class="_cw _clear _fs12  _telipsis _w100 _oh _pr10 _a6 _m10 _zi9">
								<img ng-src="@{{ product.owner_photo }}" class="_brds50 _mr5">
								@{{ product.owner_name }}
							</span>

							<div class="_bgbt2 _af _posa _zi1"></div>

						</a>
					</div>

				</div>

				<div class="row _mb15" ng-if="vm.stream.items.length">

					<div class="_c2 _fs15 _mb10 _ml15">
						ელექტრონიკა
					</div>

					<div class="col-lg-3 col-sm-4 col-xs-6 _cxxs12 _pb15 _mb10 _crp" ng-repeat="product in vm.stream.items">
						<a class="_clear _brds3 _hvrcard _posr" ng-href="@{{ product.url }}">

							<img ng-src="@{{ product.photo }}" class="_db _w100">

							<div class="_pl10 _pr15 _pt10 _pb10 _a0 _zi9">
								<span class="_cb _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18 _cw" ng-bind="product.name"></span>
							</div>

							<span class="_cw _clear _fs12  _telipsis _w100 _oh _pr10 _a6 _m10 _zi9">
								<img ng-src="@{{ product.owner_photo }}" class="_brds50 _mr5">
								@{{ product.owner_name }}
							</span>

							<div class="_bgbt2 _af _posa _zi1"></div>

						</a>
					</div>

				</div>

				<div class="_tac _pt15 _mt70 _c3" ng-if="!vm.stream.items.length">
					<h5 class="_fw400">@lang('There is no collections to show.')</h5>
				</div>
			</div>

		</div>

	</div>

</div>

@endsection
