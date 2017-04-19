@extends('user::settings.layout')

@section('title')
{{ trans('user::settings.account.title')}}
@endsection

@section('settings')

<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/account') }}">
	{{ csrf_field() }}

	<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
		<label for="username" class="col-md-4 control-label">
			{{ trans('user::settings.account.username')}}
		</label>

		<div class="col-md-6">
			<input id="username" type="text" class="form-control" name="username" value="{{ $user->username }}" required autofocus>

			@if ($errors->has('username'))
			<span class="help-block">
				<strong>{{ $errors->first('username') }}</strong>
			</span>
			@else
			<small>https://nixler.pl/{{ '@'.$user->username }}</small>
			@endif
		</div>
	</div>

	<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		<label for="name" class="col-md-4 control-label">{{ trans('user::settings.account.name')}}</label>

		<div class="col-md-6">
			<input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>

			@if ($errors->has('name'))
			<span class="help-block">
				<strong>{{ $errors->first('name') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group{{ $errors->has('headline') ? ' has-error' : '' }}">
		<label for="headline" class="col-md-4 control-label">
			{{ trans('user::settings.account.headline')}}
		</label>

		<div class="col-md-6">
			<input id="headline" type="text" class="form-control" name="headline" value="{{ $user->getMeta('headline') }}">

			@if ($errors->has('headline'))
			<span class="help-block">
				<strong>{{ $errors->first('headline') }}</strong>
			</span>
			@else
			<small>{{ trans('user::settings.account.headline_helper')}}</small>
			@endif
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<button type="submit" class="btn btn-primary">
				{{ trans('user::settings.account.save')}}
			</button>
		</div>
	</div>
</form>

@endsection