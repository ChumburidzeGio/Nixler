@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">

		<div class="col-md-3">
				<a class="_lim _hvrd _cg _brds3{{ request()->is('*/account') ? ' _hvrda' : ''}}" href="{{ url('settings/account') }}">
					<i class="material-icons _mr15 _va6 _fs20">info</i> 
					@lang('Account settings')
				</a>
				<a class="_lim _hvrd _cg _brds3{{ request()->is('*/orders') ? ' _hvrda' : ''}}" href="{{ route('settings.orders') }}">
					<i class="material-icons _mr15 _va6 _fs20">shopping_basket</i> 
					@lang('My orders')
				</a>
				<a class="_lim _hvrd _cg _brds3{{ request()->is('*/shipping') ? ' _hvrda' : ''}}" href="{{ route('shipping.settings') }}">
					<i class="material-icons _mr15 _va6 _fs20">work</i> 
					@lang('Merchant settings')
				</a>
				<a class="_lim _hvrd _cg _brds3{{ request()->is('stock') ? ' _hvrda' : ''}}" href="{{ route('stock') }}">
					<i class="material-icons _mr15 _va6 _fs20">store_mall_directory</i> @lang('My Products')
				</a>
		</div>

		<div class="col-md-9">
					@yield('layout')
		</div>
	</div>
</div>

@endsection