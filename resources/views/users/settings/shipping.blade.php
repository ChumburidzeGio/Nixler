@extends('users.settings.layout')

@section('layout')

<div ng-controller="ShipSettingsCtrl as vm">
	<script>window.cities = <?php echo json_encode($country->cities); ?></script>

	<div class="_z013 _bgw _brds2 _mb15 _clear">

		<form class="_fg" name="product" action="{{ route('shipping.settings.general') }}" method="POST"">
			{{ csrf_field() }}

			<div class="_p15 _bb1 _posr">
				<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
					@lang('General settings')
				</h1>
			</div>

			<div class="_li _hvrl row" ng-click="vm.delivery_full=!vm.delivery_full" ng-init="vm.delivery_full={{ auth()->user()->getMeta('delivery_full', 0) }}" id="delivery_full">
				<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.delivery_full }" ng-bind="vm.delivery_full ? 'check_box' : 'check_box_outline_blank'"></i>
				<input type="hidden" name="delivery_full" ng-value="vm.delivery_full | boolean">
				@lang('Delivery accross the country')
				
			</div>

			<div class="_li _hvrl row" ng-click="vm.has_return=!vm.has_return" ng-init="vm.has_return={{ auth()->user()->getMeta('has_return', 0) }}" id="has_return">
				<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.has_return }" ng-bind="vm.has_return ? 'check_box' : 'check_box_outline_blank'"></i>
				<input type="hidden" name="has_return" ng-value="vm.has_return | boolean">
				@lang('We accept return')
			</div>

			<div class="_mb15 col-sm-12 _pl15 _pr15 _pt10 _bt1 form-group _m0 ng-cloak" ng-if="vm.has_return">
				<small class="_clear _pb5">@lang('Return Policy')</small>
				<textarea name="policy" type="text" class="_b1 _bcg _fe _brds3"
				ng-disabled="!vm.has_return" msd-elastic ng-model="vm.policy"
				placeholder="@lang('Please tell users about your return policy, in how many days is it possible to return product and what are the rules')" id="policy">{{ auth()->user()->getMeta('return_policy') }}</textarea>
			</div>

			<div class="col-sm-12 _mb10 _tar _bt1 _pt10">
				<button class="_btn _bga _cb _hvra _mr5" type="submit" id="update"> 
					<i class="material-icons _mr5 _va5 _fs20">refresh</i> @lang('Update')
				</button>
			</div>

		</form>

	</div>


	<div class="_z013 _bgw _brds2">

		<form class="_fg" name="product" action="{{ route('shipping.settings.locations.create') }}" method="POST" id="add_new_form">
			{{ csrf_field() }}

			<div class="_p15 _bb1 _posr">
				<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
					@lang('Add new shipping location')
				</h1>
			</div>

			<div class="_p15 _pb0">


				<div class="row">

					<div class="col-sm-3 _mb15" ng-if="vm.cities">
						<select id="city" selector model="vm.location_id" value-attr="id" label-attr="name" class="_b1 _bcg _brds3"
						options="vm.cities" placeholder="@lang('Location')" ng-init="{{ old('location_id') ? 'vm.location_id='.old('location_id') : ''}}">
					</select>

					<input type="hidden" name="location_id" ng-value="vm.location_id">

					@if ($errors->has('location_id'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('location_id') }}</span>
					@endif
				</div>
				
				<div class="col-sm-2 _mb15">
					<input ng-init="{{ old('price') ? 'vm.price='.old('price') : ''}}" class="_b1 _bcg _fe _brds3 _fes" type="text" 
					ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }} " 
					ng-model="vm.price" placeholder="@lang('Price')" name="price">

					<input type="hidden" name="price" ng-value="vm.price">
					
					@if ($errors->has('price'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('price') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input ng-init="{{ old('window_from') ? 'vm.from='.old('window_from') : ''}}" class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.from" placeholder="@lang('From')" ui-numeric-input min="0" max="60" max-length="2" id="add_window_from">

					<input type="hidden" name="window_from" ng-value="vm.from">

					@if ($errors->has('window_from'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('window_from') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input ng-init="{{ old('window_to') ? 'vm.to='.old('window_to') : ''}}" class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.to" placeholder="@lang('To')" ui-numeric-input min="0" max="60" max-length="2" id="add_window_to"> 

					<input type="hidden" name="window_to" ng-value="vm.to">

					@if ($errors->has('window_to'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('window_to') }}</span>
					@endif
				</div>


				<div class="col-sm-3 _mb15 _oh _tac">
					<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish"> 
						<i class="material-icons _mr5 _va5 _fs20">add</i> @lang('Add')
					</button>
				</div>

			</div>

		</div>

	</form>

</div>



<div class="_z013 _bgw _brds2 _mt15">

	<div class="_p15 _posr">
		<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
			@lang('Shipping locations')
		</h1>
	</div>

	<div class="_bt1 _bb1 _bcg _pb0">


		<div class="row">
			<div class="_p15 _pb0 _bt1">

				<div class="col-sm-3">
					<small class="_clear _pb5">@lang('Location')</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">@lang('Price')</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">@lang('Time from')</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">@lang('Time to')</small>
				</div>

				<div class="col-sm-3 _tac">
					<small class="_clear _pb5">@lang('Save / Delete')</small>
				</div>

			</div>
		</div>

		@if($prices->count())
		@foreach($prices as $price)

		<form class="_fg" name="{{ $price->sid }}" 
			action="{{ route('shipping.settings.locations.update', ['id' => $price->id]) }}" 
			method="POST"">

			{{ csrf_field() }}

			<div class="row">
				<div class="_p15 _pb0 _bt1">

					<div class="col-sm-3 _mb15 _pt5">
						{{ $price->city->name }}
						<input type="hidden" name="location_id" ng-value="{{ $price->city->id }}">
					</div>

					<div class="col-sm-2 _mb15">
						<input  ng-init="{{ $price->price ? 'vm.loc'.$price->sid.'.price='.$price->price : ''}}" class="_b1 _bcg _fe _brds3 _fes" 
						type="text" ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }}" 
						ng-model="vm.loc{{ $price->sid }}.price" placeholder="@lang('Price')">

						<input type="hidden" name="price" ng-value="vm.loc{{ $price->sid }}.price">

						@if ($errors->{$price->sid}->has('price'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$price->sid}->first('price') }}</span>
						@endif
					</div>

					<div class="col-sm-2 _mb15">
						<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" 
						ng-model="vm.loc{{ $price->id }}.from" placeholder="@lang('From')" ui-numeric-input min="0" max="60" max-length="2">

						<input type="hidden" name="window_from" ng-value="vm.loc{{ $price->id }}.from" 
						ng-init="vm.loc{{ $price->id }}.from={{ $price->window_from }}">

						@if ($errors->{$price->sid}->has('window_from'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$price->sid}->first('window_from') }}</span>
						@endif
					</div>

					<div class="col-sm-2 _mb15">
						<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.loc{{ $price->id }}.to" placeholder="@lang('To')" ui-numeric-input min="0" max="60" max-length="2"> 

						<input type="hidden" name="window_to" ng-value="vm.loc{{ $price->id }}.to" ng-init="vm.loc{{ $price->id }}.to={{ $price->window_to }}">

						@if ($errors->{$price->sid}->has('window_to'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$price->sid}->first('window_to') }}</span>
						@endif
					</div>

					<input type="hidden" name="type" value="city">


					<div class="col-sm-3 _mb15 _oh _tac">
						<button class="_btn _bga _cb _hvra _pl5 _pr5 _dib" type="submit" name="action" value="save">@lang('Save')</button>
						<button class="_btn _bg5 _cb _hvra _pl5 _pr5 _dib _ml5" type="submit" name="action" value="delete">
							@lang('Delete')
						</button>
					</div>

				</div>
			</div>
		</form>

		@endforeach

		@else

		<div class="row _tac _p15">
			@lang('You have not added any location yet')
		</div>
		@endif

	</div>

	@if(auth()->user()->getMeta('delivery_full'))
	<div class="_p15 _pb0" ng-if="vm.delivery_full">

		<form class="_fg" action="{{ route('shipping.settings.locations.update', ['id' => $country_price->id]) }}" method="POST"">
			{{ csrf_field() }}

			<div class="row">
				<div class="col-sm-3 _mb15 _pt5">
					Other regions
					<input type="hidden" name="location_id" ng-value="{{ $country->id }}">
				</div>

				<div class="col-sm-2 _mb15">
					<input ng-init="{{ $country_price->price ? 'vm.loc'.$country_price->sid.'.price='.$country_price->price : ''}}" class="_b1 _bcg _fe _brds3 _fes" 
					type="text" ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }}" 
					ng-model="vm.loc{{ $country_price->sid }}.price" placeholder="@lang('Price')">

					<input type="hidden" name="price" ng-value="vm.loc{{ $country_price->sid }}.price">

					@if ($errors->{$country_price->sid}->has('price'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$country_price->sid}->first('price') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" 
					ng-model="vm.loc{{ $country_price->id }}.from" placeholder="@lang('From')" ui-numeric-input min="0" max="60" max-length="2">

					<input type="hidden" name="window_from" ng-value="vm.loc{{ $country_price->id }}.from" 
					ng-init="vm.loc{{ $country_price->id }}.from={{ $country_price->window_from }}">

					@if ($errors->{$country_price->sid}->has('window_from'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$country_price->sid}->first('window_from') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.loc{{ $country_price->id }}.to" placeholder="@lang('To')" ui-numeric-input min="0" max="60" max-length="2"> 

					<input type="hidden" name="window_to" ng-value="vm.loc{{ $country_price->id }}.to" ng-init="vm.loc{{ $country_price->id }}.to={{ $country_price->window_to }}">

					@if ($errors->{$country_price->sid}->has('window_to'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->{$country_price->sid}->first('window_to') }}</span>
					@endif
				</div>

				<input type="hidden" name="type" value="country">

				<div class="col-sm-3 _mb15 _tac">
					<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="save"> 
						<i class="material-icons _mr5 _va5 _fs20">refresh</i> @lang('Save')
					</button>
				</div>

			</div>

		</form>

	</div>
	@endif

</div>
@endsection