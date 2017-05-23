@extends('users.settings.layout')

@section('layout')

<div ng-controller="AccountSettingsCtrl as vm">
	<script>window.cities = <?php echo json_encode($cities); ?></script>
	<div class="_bs012 _bgw _mb15 _db">

		<div class="_p15 _bb1 _posr">
			<h2 class="_fs16 _cg _lh1 _db _telipsis _m0 _ml5">
				@lang('General settings')
			</h2>
		</div>

		<div class="_p15">
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/account') }}">
				{{ csrf_field() }}

				<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="name" class="col-md-4 control-label">@lang('Name')</label>

					<div class="col-md-6">
						<input id="name" type="text" class="_b1 _bcg _brds3 _fe _fes" name="name" value="{{ $user->name }}" required autofocus>

						@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
					<label for="username" class="col-md-4 control-label">
						@lang('Username')
					</label>

					<div class="col-md-6">
						<input id="username" type="text" class="_b1 _bcg _brds3 _fe _fes" name="username" value="{{ $user->username }}" required autofocus>

						@if ($errors->has('username'))
						<span class="help-block">
							<strong>{{ $errors->first('username') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email" class="col-md-4 control-label">@lang('Email')</label>

					<div class="col-md-6">
						<input id="email" type="text" class="_b1 _bcg _brds3 _fe _fes" name="email" value="{{ $user->email }}" required autofocus>

						@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">

					<label for="phone" class="col-md-4 control-label">@lang('Phone')</label>

					<div class="col-md-6">

						   @if(!$user->verified and $user->phone and $user->getMeta('phone_vcode'))
						   <div class="_b1 _bgwt4 _c3 _bcg _clear _p15 _mb10 _brds2">
							<p class="_clear">@lang("A 5-digit activation code will be texted to your phone within a few minutes.")</p>

								<small class="_clear _pb5">@lang("Confirmation code")</small>
								<div class="row">
								<div class="col-xs-7">

									<input 
									id="pcode" 
									type="text" 
									name="pcode" 
									onkeyup="this.value=this.value.replace(/[^\d]/,'')"
									pattern=".{6}" 
									maxlength="6" 
									required="1" 
									class="_b1 _bcg _fe _brds3 _fes" 
									autocomplete="off"> 

									@if ($errors->has('pcode'))
									<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('pcode') }}</span>
									@endif
								</div>

								<div class="col-xs-3">
									<button class="_btn _bgi _cw">
										@lang("Verify")
									</button>
								</div>
								</div>
							</div>
							@endif


							<input id="phone" 
								type="text" 
								class="_b1 _bcg _brds3 _fe _fes" 
								name="phone" 
								value="{{ $user->phone }}"
								onkeyup="this.value=this.value.replace(/[^\d]/,'')">

							@if ($errors->has('phone'))
							<span class="help-block">
								<strong>{{ $errors->first('phone') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
						<label for="city" class="col-md-4 control-label">
							@lang('City')
						</label>

						<div class="col-md-6">
							<select selector model="vm.city_id" value-attr="id" label-attr="name" class="_b1 _bcg _brds3" options="vm.cities" ng-init="vm.city_id={{ old('city_id', $user->city_id) ? : 0}}" require="1" id="city">
							</select>

							<input type="hidden" name="city_id" ng-value="vm.city_id">

							@if ($errors->has('city'))
							<span class="help-block">
								<strong>{{ $errors->first('city') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('website') ? ' has-error' : '' }}">
						<label for="website" class="col-md-4 control-label">
							@lang('Website')
						</label>

						<div class="col-md-6">
							<input id="website" type="text" class="_b1 _bcg _brds3 _fe _fes" name="website" value="{{ $user->getMeta('website') }}" placeholder="https://www.example.com">

							@if ($errors->has('website'))
							<span class="help-block">
								<strong>{{ $errors->first('website') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('headline') ? ' has-error' : '' }}">
						<label for="headline" class="col-md-4 control-label">
							@lang('Headline')
						</label>

						<div class="col-md-6">
							<input id="headline" type="text" class="_b1 _bcg _brds3 _fe _fes" name="headline" value="{{ $user->getMeta('headline') }}" placeholder="@lang('What describes you the best?')">

							@if ($errors->has('headline'))
							<span class="help-block">
								<strong>{{ $errors->first('headline') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 col-md-offset-4">
							<button type="submit" class="_btn _bgi _cw">
								@lang('Save')
							</button>
						</div>
					</div>
				</form>
			</div>

		</div>



		<div class="_bs012 _bgw _mb15 _clear">

			<div class="_p15 _bb1 _posr">
				<h2 class="_fs16 _cg _lh1 _db _telipsis _m0 _ml5">
					@lang('Update Password')
				</h2>
			</div>

			<div class="_p15">
				<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/password') }}">
					{{ csrf_field() }}

					@if(auth()->user()->password)
					<div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
						<label for="current_password" class="col-md-4 control-label">
							@lang('Current password')
						</label>

						<div class="col-md-6">
							<input id="current_password" type="password" class="_b1 _bcg _brds3 _fe _fes" name="current_password" required autofocus>

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
							@lang('New password')
						</label>

						<div class="col-md-6">
							<input id="new_password" type="password" class="_b1 _bcg _brds3 _fe _fes" name="new_password" required autofocus>

							@if ($errors->has('new_password'))
							<span class="help-block">
								<strong>{{ $errors->first('new_password') }}</strong>
							</span>
							@endif
						</div>
					</div>


					<div class="form-group{{ $errors->has('new_password_confirmation') ? ' has-error' : '' }}">
						<label for="new_password_confirmation" class="col-md-4 control-label">
							@lang('Verify password')
						</label>

						<div class="col-md-6">
							<input id="new_password_confirmation" type="password" class="_b1 _bcg _brds3 _fe _fes" name="new_password_confirmation" required autofocus>

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
								@lang('Save')
							</button>
						</div>
					</div>
				</form>
			</div>

		</div>

		<div class="_bs012 _bgw _mb15 _clear">

			<div class="_p15 _bb1 _posr">
				<h1 class="_fs16 _cg _lh1 _db _telipsis _m0 _ml5">
					@lang('Deactivate your account')
				</h1>
			</div>

			<div class="_p15">
				@lang('Would you like to deactivate your profile?')
				<button class="_btn _bg3 _cw _right" type="submit" ng-click="vm.deactivateAccount()"> 
					<i class="material-icons _mr5 _va5 _fs20">delete</i> @lang('Deactivate account')
				</button>
			</div>

		</div>

		<script type="text/ng-template" id="deactivateAccountConfirm.html">
			<div class="_brds3">
				<div class="_bb1 _p15 _fs16 _pb10 _cb">@lang('Are you sure you want to deactivate your account?')</div>
				<p class="_clear _fs14 _p15 _lh1">
				@lang('If you deactivate your account your profile won’t be visible to other people on Nixler and people won’t be able to search for you. Some information, such as messages you sent, may still be visible to others.')<br><br>
				@lang('If you’d like to come back to Nixler after you’ve deactivated your account, you can reactivate your account at anytime by logging in with your email and password or with oher auth provider.')<br><br>
				@lang('Keep in mind, if you use your Nixler account to log into Nixler your account will be reactivated. This means your Nixler profile, including things like your followers, products, photos and orders, will be completely restored.')
				</p>
				<div class="_p15 _tar">
					<button type="button" class="_btn _bgi _cw _mr5" ng-click="closeThisDialog(0)">
						@lang('Cancel')
					</button>
					<button type="button" class="_btn _bg5 _c2" ng-click="confirm(1)">@lang('Deactivate')</button>
				</div>
			</div>
		</script>

	</div>

	@endsection