@extends('user::settings.layout-basic')

@section('layout')

<div class="_z013 _bgw _brds2 _mb15 _clear">

		<div class="_p15 _bb1 _posr">
			<h2 class="_fs16 _c3 _telipsis _m0">
				{{ trans('user::settings.password.title')}}
			</h2>
		</div>

		<div class="_p15">
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
						<button type="submit" class="_btn _bgi _cw">
							{{ trans('user::settings.password.save')}}
						</button>
					</div>
				</div>
			</form>
		</div>

</div>

@endsection