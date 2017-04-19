@extends('user::settings.layout-basic')

@section('title')
Addresses
@endsection

@section('layout')

<div ng-controller="AddressSettingsCtrl as vm">
<script>window.cities = <?php echo json_encode($country->cities); ?></script>

<div class="_z013 _bgw _brds2">

	<form class="_fg" name="product" action="{{ route('settings.addresses.store') }}" method="POST"">
		{{ csrf_field() }}

		<div class="_p15 _bb1 _posr">
			<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
				Add new address
			</h1>
		</div>

		<div class="_p15 _pb0">


			<div class="row">

				<div class="col-xs-4 _mb15" ng-if="vm.cities">
					<select selector model="vm.location_id" value-attr="id" label-attr="name" class="_b1 _bcg _brds3"
						options="vm.cities" placeholder="City" ng-init="vm.location_id={{ old('city_id') ? : '-1'}}">
					</select>

					<input type="hidden" name="city_id" ng-value="vm.location_id">

					@if ($errors->has('city_id'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('city_id') }}</span>
					@endif
				</div>
				
				<div class="col-xs-6 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Street address, block, flat" name="street">

					@if ($errors->has('street'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('street') }}</span>
					@endif
				</div>

				<div class="_mb15 col-xs-2">
					<button class="_btn _bga _cb _hvra _ml10 _right" type="submit" name="action" value="publish"> 
						<i class="material-icons _mr5 _va5 _fs20">add</i> Add
					</button>
				</div>

			</div>

		</div>

	</form>

</div>


<div class="_z013 _bgw _brds2 _mt15">

	<div class="_p15 _posr _bb1 _bcg">
		<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
			Your addresses
		</h1>
	</div>

	<div class="_bcg _pb0">

		@if($addresses->count())
		@foreach($addresses as $address)
		<li class="_li _hvrl">

		    <div class="_pl5 _media _clear">
		      <a class="_oh" ng-click="vm.edit(i.id)" href="{{ route('settings.addresses.edit', ['id' => $address->id]) }}">
		        <span class="_title _fs14 _ci">{{ $address->street }}</span>
		        <small class="_clear">{{ $address->city->name }}</small>
		      </a>
		      <span class="_ar _posa _m15 _pt5" 
		      	ng-click="vm.delete('{{ route('settings.addresses.delete', ['id' => $address->id]) }}')" 
		      	confirm-click="Do you really want to delete address?">
		          <i class="material-icons _fs20">delete</i>
		        </span>
		    </div>

		</li>
		@endforeach

		@else

		<div class="row _tac _p15">
			You have not added any location yet
		</div>
		@endif

	</div>

</div>
@endsection