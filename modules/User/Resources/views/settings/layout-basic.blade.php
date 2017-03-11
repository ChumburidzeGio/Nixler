@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">

		<div class="col-md-3">
			<div class="_bgw _b1 _brds3">
				<a class="_lim _hvrd _cg _bb1" href="{{ url('settings/account') }}">
					<i class="material-icons _mr15 _va7 _fs20">info</i> 
					{{ trans('user::settings.account.title')}}
				</a>
				<a class="_lim _hvrd _cg _bb1" href="{{ url('settings/social') }}">
					<i class="material-icons _mr15 _va7 _fs20">link</i> 
					{{ trans('user::settings.social.title')}}
				</a>
				<a class="_lim _hvrd _cg _bb1" href="{{ url('settings/password') }}">
					<i class="material-icons _mr15 _va7 _fs20">fingerprint</i> 
					{{ trans('user::settings.password.title')}}
				</a>
				<a class="_lim _hvrd _cg" href="{{ url('settings/emails') }}">
					<i class="material-icons _mr15 _va7 _fs20">email</i> 
					{{ trans('user::settings.emails.title')}}
				</a>
			</div>
		</div>

		<div class="col-md-7">
					@yield('layout')
		</div>
	</div>
</div>

@endsection