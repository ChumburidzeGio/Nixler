@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">

		<div class="col-md-3">
			<div class="_bgw _z013 _brds3">
				<a class="_lim _hvrd _cg" href="{{ url('settings/account') }}">
					<i class="material-icons _mr15 _va7 _fs20">info</i> 
					{{ trans('user::settings.account.title')}}
				</a>
				<a class="_lim _hvrd _cg" href="{{ url('settings/social') }}">
					<i class="material-icons _mr15 _va7 _fs20">link</i> 
					{{ trans('user::settings.social.title')}}
				</a>
				<a class="_lim _hvrd _cg" href="{{ url('settings/password') }}">
					<i class="material-icons _mr15 _va7 _fs20">fingerprint</i> 
					{{ trans('user::settings.password.title')}}
				</a>
				<a class="_lim _hvrd _cg" href="{{ url('settings/emails') }}">
					<i class="material-icons _mr15 _va7 _fs20">email</i> 
					{{ trans('user::settings.emails.title')}}
				</a>
				<a class="_lim _hvrd _cg" href="{{ url('settings/phones') }}">
					<i class="material-icons _mr15 _va7 _fs20">phone</i> 
					{{ trans('user::settings.phones.title')}}
				</a>
				@foreach(config('settings.menu') as $item)
				<a class="_lim _hvrd _cg" href="{{ route($item['route']) }}">
					<i class="material-icons _mr15 _va7 _fs20">{{ $item['icon'] }}</i> 
					{{ trans($item['title']) }}
				</a>
				@endforeach
			</div>
		</div>

		<div class="col-md-9">
					@yield('layout')
		</div>
	</div>
</div>

@endsection