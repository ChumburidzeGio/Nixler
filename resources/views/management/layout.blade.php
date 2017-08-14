@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">

		<div class="col-md-3">
			<div class="_lst _mb15">
				<a class="_lsti {{ request()->is('*/users') ? 'active' : ''}}" href="{{ url('management/users') }}">
					<i class="material-icons">people</i>
					@lang('People')
				</a>
				<a class="_lsti {{ request()->is('*/products') ? 'active' : ''}}" href="{{ url('management/products') }}">
					<i class="material-icons">store_mall_directory</i>
					@lang('Products')
				</a>
				<a class="_lsti {{ request()->is('*/orders') ? 'active' : ''}}" href="{{ url('management/orders') }}">
					<i class="material-icons">shopping_basket</i>
					@lang('Orders')
				</a>
			</div>
		</div>

		<div class="col-md-9">
					@yield('layout')
		</div>
	</div>
</div>

@endsection