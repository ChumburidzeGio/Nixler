@extends('layouts.app')

@section('content')

<div class="container">

		<div class="row">

			<div class="col-md-10 col-md-offset-1">

				<div class="_clear _bgw _z013 _brds3">

					<form class="_fg" action="{{ url('search') }}" ng-init="vm.query='{{ request()->input('query') }}'">
						<input ng-model="vm.query" class="_fe _brds3" placeholder="Start typing name of product or account" style="padding-left: 45px;" autofocus="on" name="query" minlength="3" required="">
						<i class="material-icons _a8 _mt10 _fs20 _ml22">search</i>
					</form>

					{{--<div class="_clear" ng-if="!vm.autocomplate.data">
						<a class="_li _clear _hvr1 m" ng-click="vm.complateWith(item.name)" ng-repeat="item in vm.autocomplate.data | filter:vm.query" ng-bind="item.name" title="@{{ item.name }}"></a>
					</div>

					<div class="_clear _tbs _bt1" ng-if="!vm.autocomplate.data.length">
						<a class="_tb _hvr1 _pl3 _pr3" ui-sref="app.search({query: vm.stateQuery, index: 'products'})" ui-sref-active="_c4 _bb1 _bs2 _bc4">Products</a>
						<a class="_tb _hvr1 _pl3 _pr3" ui-sref="app.search({query: vm.stateQuery, index: 'accounts'})" ui-sref-active="_c4 _bb1 _bs2 _bc4">Accounts</a>
						<a class="_tb _hvr1 _pl3 _pr3" ui-sref="app.search({query: vm.stateQuery, index: 'collections'})" ui-sref-active="_c4 _bb1 _bs2 _bc4">Collections</a>
						<span class="_right _d2 _tb _fs-1 _c1" ng-if="vm.data.total">
							<span ng-bind="vm.data.total"></span> results for
							<span ng-bind="vm.data.time"></span> ms
						</span>
					</div>--}}

				</div>


				<div class="row _mt15">
				@each('product::short-card', $products, 'product')
				</div>

			</div>


		</div>
</div>

@endsection
