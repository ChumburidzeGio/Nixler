@extends('users.settings.layout')

@section('layout')

<div ng-controller="ShipSettingsCtrl as vm">
	<script>
		window.cities = <?php echo json_encode($country->cities); ?>;
	</script>

	<script>window.settings = <?php echo json_encode([
		'delivery_full' => !!old('delivery_full', auth()->user()->getMeta('delivery_full', 0)),
		'has_return' => !!old('has_return', auth()->user()->getMeta('has_return', 0)),
		'has_sku' => !!old('has_sku', auth()->user()->getMeta('has_sku', 0)),
		'cod' => !!old('cod', auth()->user()->getMeta('cod', 0)),
		'bank_transaction' => !!old('bank_transaction', auth()->user()->getMeta('bank_transaction', 0)),
		'policy' => old('policy', auth()->user()->getMeta('policy', '')),
		'bank_credentials' => old('bank_credentials', auth()->user()->getMeta('bank_credentials', '')),
		'location_id' => old('location_id'),
		'price' => old('price'),
		'window_from' => old('window_from'),
		'window_to' => old('window_to'),
		]); ?></script>

		@if(auth()->user()->shippingPrices()->count() && request()->input('ref') == 'product-edit')
		<div class="_card _z013 _mb15 _bgw _p15 _fs15 _cb"> 
			@lang('You can now sell the products. Let\'s just publish and we will take care of the rest!')
			<div class="_clear _mt15">
				<a class="_btn _bga _cb _mr5" href="{{ url('new-product') }}">@lang('Add new product')</a>
			</div>
		</div>
		@endif

		<div class="_crd _mb15">
			<h2 class="_crd-header">@lang('General settings')</h2>
			<div class="_crd-content">

				<form class="_fg" name="product" action="{{ route('shipping.settings.general') }}" method="POST">
					{{ csrf_field() }}
					<input type="hidden" name="ref" value="{{ request()->input('ref') }}">

					<div class="_li _hvrl row" ng-click="vm.delivery_full=!vm.delivery_full" id="delivery_full">
						<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.delivery_full }" ng-bind="vm.delivery_full ? 'check_box' : 'check_box_outline_blank'">check_box</i>
						<input type="hidden" name="delivery_full" ng-value="vm.delivery_full | boolean">
						@lang('Delivery accross the country')

					</div>

					<div class="_li _hvrl row" ng-click="vm.has_return=!vm.has_return" id="has_return">
						<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.has_return }" ng-bind="vm.has_return ? 'check_box' : 'check_box_outline_blank'">check_box</i>
						<input type="hidden" name="has_return" ng-value="vm.has_return | boolean">
						@lang('We accept return')
					</div>

					<div class="_li _hvrl row" ng-click="vm.has_sku=!vm.has_sku" id="has_sku">
						<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.has_sku }" ng-bind="vm.has_sku ? 'check_box' : 'check_box_outline_blank'">check_box</i>
						<input type="hidden" name="has_sku" ng-value="vm.has_sku | boolean">
						@lang('Add to products SKU (stock-keeping unit)')
					</div>

					<div class="_mb15 _pl15 _pr15 _pt10 _bt1 form-group _m0">
						<small class="_clear _pb5">@lang('Terms & Conditions')</small>
						<textarea name="policy" type="text" class="_b1 _bcg _fe _brds3" msd-elastic ng-model="vm.policy"
						placeholder="@lang('Describe your return policy and shipping conditions')" id="policy"></textarea>
					</div>

					<div class="_bt1 _pt10 _clear">
						<button class="_btn _bga _cb _hvra _mr15 _right _dib _mb10" type="submit" id="update" ng-disabled="!vm.changed"> 
							<i class="material-icons _mr5 _va5 _fs20">refresh</i> @lang('Update')
						</button>
					</div>
				</form>

			</div>
		</div>


	{{-- <!--div class="_z013 _bgw _brds2 _mb15 _clear">

		<form class="_fg" name="product" action="{{ route('shipping.settings.payment') }}" method="POST">
			{{ csrf_field() }}
			<input type="hidden" name="ref" value="{{ request()->input('ref') }}">

			<div class="_p15 _bb1 _posr">
				<h1 class="_fs18 _ci _lh1 _telipsis _m0">
					@lang('Payment methods')
				</h1>
			</div>

			<div class="_li _hvrl row" ng-click="vm.cod=!vm.cod" id="cod">
				<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.cod }" ng-bind="vm.cod ? 'check_box' : 'check_box_outline_blank'">check_box</i>
				<input type="hidden" name="cod" ng-value="vm.cod | boolean">
				@lang('Accept cache on delivery')
				
			</div>

			<div class="_li _hvrl row" ng-click="vm.bank_transaction=!vm.bank_transaction" id="bank_transaction">
				<i class="material-icons _left _mr15 _fs20 _c2 _anim1 _ml15 _va7" ng-class="{'_c4': vm.bank_transaction }" ng-bind="vm.bank_transaction ? 'check_box' : 'check_box_outline_blank'">check_box</i>
				<input type="hidden" name="bank_transaction" ng-value="vm.bank_transaction | boolean">
				@lang('Accept bank transaction')
			</div>

			<div class="_mb15 col-sm-12 _pl15 _pr15 _pt10 _bt1 form-group _m0" ng-if="vm.bank_transaction">
				<small class="_clear _pb5">@lang('Bank account details and instruction for sending money')</small>
				<textarea name="bank_credentials" type="text" class="_b1 _bcg _fe _brds3" msd-elastic ng-model="vm.bank_credentials" id="bank_credentials" placeholder='@lang("f.e. Receiver: Merchant LLC \nIBAN: GE07BS00120790291500")'>
				</textarea>
			</div>

			<div class="col-sm-12 _mb10 _tar _bt1 _pt10">
				<button class="_btn _bga _cb _hvra _mr5" type="submit" id="update" ng-disabled="!vm.pchanged"> 
					<i class="material-icons _mr5 _va5 _fs20">refresh</i> @lang('Update')
				</button>
			</div>

		</form>

	</div--> --}}



	<div class="_crd _mb15">
		<h2 class="_crd-header">@lang('Add new shipping location')</h2>
		<div class="_crd-content">

			<form class="_fg" name="product" action="{{ route('shipping.settings.locations.create') }}" method="POST" id="add_new_form">
				{{ csrf_field() }}

				<div class="_p15 _pb0">

					<div class="row">

						<div class="col-sm-3 _mb15" ng-if="vm.cities">
							<select id="city" selector model="vm.location_id" value-attr="id" label-attr="name" class="_b1 _bcg _brds3"
							options="vm.cities" placeholder="@lang('Location')"></select>

							<input type="hidden" name="location_id" ng-value="vm.location_id">

							@if ($errors->has('location_id'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('location_id') }}</span>
							@endif
						</div>
						
						<div class="col-sm-2 _mb15">
							<input class="_b1 _bcg _fe _brds3 _fes" type="text" 
							ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }} " 
							ng-model="vm.price" placeholder="@lang('Price')" name="price">

							<input type="hidden" name="price" ng-value="vm.price">
							
							@if ($errors->has('price'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('price') }}</span>
							@endif
						</div>

						<div class="col-sm-2 _mb15">
							<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.from" placeholder="@lang('From')" ui-numeric-input min="0" max="60" max-length="2" id="add_window_from">

							<input type="hidden" name="window_from" ng-value="vm.from">

							@if ($errors->has('window_from'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('window_from') }}</span>
							@endif
						</div>

						<div class="col-sm-2 _mb15">
							<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.to" placeholder="@lang('To')" ui-numeric-input min="0" max="60" max-length="2" id="add_window_to"> 

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
	</div>


	<div class="_crd _mb15">
		<h2 class="_crd-header">@lang('Shipping locations')</h2>
		<div class="_crd-content">

			<div class="_bb1 _bcg _pb0">

				<div class="row">
					<div class="_p15 _pb0">

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
							@lang('Other regions')
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
	</div>

</div>
@endsection