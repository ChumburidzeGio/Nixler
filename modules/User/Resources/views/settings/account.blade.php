@extends('user::settings.layout-basic')

@section('layout')

<div ng-controller="AccountSettingsCtrl as vm">
	<div class="_z013 _bgw _brds2 _mb15 _clear">

		<div class="_p15 _bb1 _posr">
			<h2 class="_fs16 _c3 _telipsis _m0">
				{{ trans('user::settings.account.title')}}
			</h2>
		</div>

		<div class="_p15">
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

				<div class="form-group{{ $errors->has('website') ? ' has-error' : '' }}">
					<label for="website" class="col-md-4 control-label">
						{{ trans('user::settings.social.website')}}
					</label>

					<div class="col-md-6">
					<input id="website" type="text" class="form-control" name="website" value="{{ $user->getMeta('website') }}" placeholder="{{ trans('user::settings.social.website_plac')}}">

						@if ($errors->has('website'))
						<span class="help-block">
							<strong>{{ $errors->first('website') }}</strong>
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
						<button type="submit" class="_btn _bgi _cw">
							{{ trans('user::settings.account.save')}}
						</button>
					</div>
				</div>
			</form>
		</div>

	</div>



	<div class="_z013 _bgw _brds2 _mb15 _clear">

		<div class="_p15 _bb1 _posr">
			<h1 class="_fs16 _c3 _telipsis _m0">
				Deactivate your account
			</h1>
		</div>

		<div class="_p15">
			Would you like to deactivate your profile ?
			<button class="_btn _bg3 _cw _right" type="submit" ng-click="vm.deactivateAccount()"> 
				<i class="material-icons _mr5 _va5 _fs20">delete</i> Deactivate account
			</button>
		</div>

	</div>

	<script type="text/ng-template" id="deactivateAccountConfirm.html">
		<div class="_brds3">
			<div class="_bb1 _p15 _fs16 _pb10 _cb">Are you sure you want to deactivate your account?</div>
			<p class="_clear _fs14 _p15 _lh1">If you deactivate your account your profile won’t be visible to other people on Nixler and people won’t be able to search for you. Some information, such as messages you sent, may still be visible to others.<br><br>
				If you’d like to come back to Nixler after you’ve deactivated your account, you can reactivate your account at anytime by logging in with your email and password or with oher auth provider. <br><br>Keep in mind, if you use your Nixler account to log into Nixler your account will be reactivated. This means your Nixler profile, including things like your followers, products, photos and orders, will be completely restored.
			</p>
			<div class="_p15 _tar">
				<button type="button" class="_btn _bgi _cw _mr5" ng-click="closeThisDialog(0)">Go back</button>
				<button type="button" class="_btn _bg5 _c2" ng-click="confirm(1)">Deactivate</button>
			</div>
		</div>
	</script>

</div>

@endsection