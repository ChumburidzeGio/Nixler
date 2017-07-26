@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">

		<div class="col-md-3">
			<div class="_lst _mb15">
				<a class="_lsti {{ request()->is('*/account') ? 'active' : ''}}" href="{{ url('settings/account') }}">
					<i class="material-icons">info</i>
					@lang('Account settings')
				</a>
				<a class="_lsti {{ request()->is('*/orders') ? 'active' : ''}}" href="{{ route('settings.orders') }}">
					<i class="material-icons">shopping_basket</i>
					@lang('My orders')
				</a>
				<a class="_lsti {{ request()->is('*/shipping') ? 'active' : ''}}" href="{{ route('shipping.settings') }}">
					<i class="material-icons">work</i>
					@lang('Merchant settings')
				</a>
				<a class="_lsti {{ request()->is('stock') ? 'active' : ''}}" href="{{ route('stock') }}">
					<i class="material-icons">store_mall_directory</i>
					@lang('My products')
				</a>

				<a class="_lsti {{ request()->is('*/sessions') ? 'active' : ''}}" href="{{ route('settings.sessions') }}">
					<i class="material-icons">important_devices</i>
					@lang('Sessions')
				</a>
			</div>
		</div>

		<div class="col-md-9">
					@yield('layout')
		</div>
	</div>
</div>

@endsection