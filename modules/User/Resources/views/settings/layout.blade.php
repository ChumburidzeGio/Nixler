@extends('user::settings.layout-basic')

@section('layout')
<div class="_bgw _b1 _bs011 _brds3 _clear _mb15">
				<span class="_clear _mb10 _fs18 _bb1 _pt10 _pb10 _pl15 _pr15">
					@yield('title')

					@if(session('status'))
					<span class="_right _cdp _fs12 _mt5">{{ session('status') }}</span>
					@endif
				</span>
				<div class="_p15">
					@yield('settings')
				</div>
</div>
@endsection