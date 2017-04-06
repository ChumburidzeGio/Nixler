@extends('layouts.app')

@section('content')

<div class="container" ng-controller="StreamCtrl as vm">

<script>window.stream = {!! $products->toJson() !!};</script>

		<div class="row">

			<div class="col-xs-12">

				<div class="_clear _bgw _z013 _brds3">

					<form class="_fg" action="{{ url('feed') }}">
						<input class="_fe _brds3" placeholder="Start typing name of product or account" style="padding-left: 45px;" autofocus="on" name="query" minlength="3" required="" value="{{ request()->input('query') }}" id="search">
						<i class="material-icons _a8 _mt10 _fs20 _ml22">search</i>
					</form>
				</div>

				<span class="_c3 _mt10 _clear">{{-- array_get($products->toArray(), 'meta.pagination.total', 0) --}}</span>

				<div class="row _mt15">

				<div class="col-md-3 _pb15" ng-repeat="product in vm.stream.data">
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

			</div>


		</div>
</div>

@endsection
