@extends('user::settings.layout-basic')

@section('title')
{{ trans('user::settings.account.title')}}
@endsection

@section('layout')

<div ng-controller="ShipSettingsCtrl as vm">
<script>window.cities = <?php echo json_encode($country->cities); ?></script>

<div class="_z013 _bgw _brds2">

	<form class="_fg" name="product" action="{{ route('shipping.settings.locations.create') }}" method="POST"">
		{{ csrf_field() }}

		<div class="_p15 _bb1 _posr">
			<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
				Add new shipping location
			</h1>
		</div>

		<div class="_p15 _pb0">


			<div class="row">

				<div class="col-sm-3 _mb15" ng-if="vm.cities">
					<select selector model="vm.location_id" value-attr="id" label-attr="name" class="_b1 _bcg _brds3"
						options="vm.cities" placeholder="Location" ng-init="vm.location_id={{ old('location_id') ? : ''}}">
					</select>

					<input type="hidden" name="location_id" ng-value="vm.location_id">

					@if ($errors->has('location_id'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('location_id') }}</span>
					@endif
				</div>
				
				<div class="col-sm-2 _mb15">
					<input ng-init="vm.price={{ old('price') }}" class="_b1 _bcg _fe _brds3 _fes" type="text" 
					ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }} " 
					ng-model="vm.price" placeholder="Price">

					<input type="hidden" name="price" ng-value="vm.price">

					@if ($errors->has('price'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('price') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.from" placeholder="From" ui-numeric-input min="0" max="60" max-length="2">

					<input type="hidden" name="window_from" ng-value="vm.from" ng-init="vm.from={{ old('window_from')}}">

					@if ($errors->has('window_from'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('window_from') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.to" placeholder="To" ui-numeric-input min="0" max="60" max-length="2"> 

					<input type="hidden" name="window_to" ng-value="vm.to" ng-init="vm.to={{ old('window_to') }}">

					@if ($errors->has('window_to'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('window_to') }}</span>
					@endif
				</div>


				<div class="col-sm-3 _mb15 _oh _tac">
					<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish"> 
						<i class="material-icons _mr5 _va5 _fs20">add</i> Add
					</button>
				</div>

			</div>

		</div>

	</form>

</div>



<div class="_z013 _bgw _brds2 _mt15">

	<div class="_p15 _posr">
		<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
			Shipping locations for Poland
		</h1>
	</div>

	<div class="_bt1 _bb1 _bcg _pb0">


		<div class="row">
			<div class="_p15 _pb0 _bt1">

				<div class="col-sm-3">
					<small class="_clear _pb5">Location</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">Price</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">Time from</small>
				</div>

				<div class="col-sm-2">
					<small class="_clear _pb5">Time to</small>
				</div>

				<div class="col-sm-3 _tac">
					<small class="_clear _pb5">Save / Delete</small>
				</div>

			</div>
		</div>

		@if($prices->count())
		@foreach($prices as $price)
		<div class="row">
			<div class="_p15 _pb0 _bt1">


				<div class="col-sm-3 _mb15 _pt5">
					{{ $price->city->name }}
				</div>

				<div class="col-sm-2 _mb15">
					<input ng-init="vm.loc{{ $price->id }}.price={{ $price->price }}" class="_b1 _bcg _fe _brds3 _fes" 
					type="text" ng-currency min="1" ng-required="true" currency-symbol="{{ $country->currency_symbol }}" 
					ng-model="vm.loc{{ $price->id }}.price" placeholder="Price">

					<input type="hidden" name="{{ $price->id }}price" ng-value="vm.loc{{ $price->id }}.price">

					@if ($errors->has($price->id.'price'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first($price->id.'price') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" 
					ng-model="vm.loc{{ $price->id }}.from" placeholder="From" ui-numeric-input min="0" max="60" max-length="2">

					<input type="hidden" name="{{ $price->id }}window_from" ng-value="vm.from" 
					ng-init="vm.loc{{ $price->id }}.from={{ $price->window_from }}">

					@if ($errors->has($price->id.'window_from'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first($price->id.'window_from') }}</span>
					@endif
				</div>

				<div class="col-sm-2 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.loc{{ $price->id }}.to" placeholder="To" ui-numeric-input min="0" max="60" max-length="2"> 

					<input type="hidden" name="{{ $price->id }}window_to" ng-value="vm.to" ng-init="vm.loc{{ $price->id }}.to={{ $price->window_to }}">

					@if ($errors->has($price->id.'window_to'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first($price->id.'window_to') }}</span>
					@endif
				</div>


				<div class="col-sm-3 _mb15 _oh _tac">
					<button class="_btn _bga _cb _hvra _pl5 _pr5 _dib" type="submit" name="action" value="publish">Save</button>
					<button class="_btn _bg5 _cb _hvra _pl5 _pr5 _dib _ml5" type="submit" name="action" value="publish">
						Delete
					</button>
				</div>

			</div>
		</div>
		@endforeach

		@else

		<div class="row _tac _p15">
			You have not added any location yet
		</div>
		@endif

	</div>

	<div class="_p15 _pb0">

		<div class="row">
			<div class="col-sm-3 _mb15 _pt5">
				Other regions
			</div>

			<div class="col-sm-2 _mb15">
				<input name="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" placeholder="Price"> 

				@if ($errors->has('title'))
				<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
				@endif
			</div>

			<div class="col-sm-2 _mb15">
				<input name="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" placeholder="From"> 

				@if ($errors->has('title'))
				<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
				@endif
			</div>

			<div class="col-sm-2 _mb15">
				<input name="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" placeholder="To"> 

				@if ($errors->has('title'))
				<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
				@endif
			</div>

			<div class="col-sm-3 _mb15">
				<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish"> 
					<i class="material-icons _mr5 _va5 _fs20">refresh</i> Update
				</button>
			</div>

		</div>

	</div>

</div>
@endsection