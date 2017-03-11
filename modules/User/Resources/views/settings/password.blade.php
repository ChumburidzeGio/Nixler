@extends('user::settings.layout')

@section('title')
{{ trans('user::settings.password.title')}}
@endsection

@section('settings')

<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/password') }}">
	{{ csrf_field() }}

	@if(auth()->user()->password)
	<div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
		<label for="current_password" class="col-md-4 control-label">
			{{ trans('user::settings.password.current')}}
		</label>

		<div class="col-md-6">
			<input id="current_password" type="password" class="form-control" name="current_password" required autofocus>

			@if ($errors->has('current_password'))
			<span class="help-block">
				<strong>{{ $errors->first('current_password') }}</strong>
			</span>
			@endif
		</div>
	</div>
	<hr>
	@endif

	<div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
		<label for="new_password" class="col-md-4 control-label">
			{{ trans('user::settings.password.new')}}
		</label>

		<div class="col-md-6">
			<input id="new_password" type="password" class="form-control" name="new_password" required autofocus>

			@if ($errors->has('new_password'))
			<span class="help-block">
				<strong>{{ $errors->first('new_password') }}</strong>
			</span>
			@endif
		</div>
	</div>


	<div class="form-group{{ $errors->has('new_password_confirmation') ? ' has-error' : '' }}">
		<label for="new_password_confirmation" class="col-md-4 control-label">
			{{ trans('user::settings.password.verify')}}
		</label>

		<div class="col-md-6">
			<input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required autofocus>

			@if ($errors->has('new_password_confirmation'))
			<span class="help-block">
				<strong>{{ $errors->first('new_password_confirmation') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<button type="submit" class="btn btn-primary">
				{{ trans('user::settings.password.save')}}
			</button>
		</div>
	</div>
</form>

@endsection