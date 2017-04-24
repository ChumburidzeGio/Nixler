@extends('user::settings.layout-basic')

@section('title')
Addresses
@endsection

@section('layout')

<div class="_z013 _bgw _brds2">

	<form class="_fg" name="product" action="{{ route('settings.addresses.update', ['id' => $address->id]) }}" method="POST"">
		{{ csrf_field() }}

		<div class="_p15 _bb1 _posr">
			<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
				{{ trans('address::addresses.settings.edit_address') }}
			</h1>
		</div>

		<div class="_p15 _pb0">

			<div class="row">
				
				<div class="col-sm-9 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="{{ trans('address::addresses.settings.address_placeholder') }}" name="street"
					value="{{ $address->street }}">

					@if ($errors->has('street'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('street') }}</span>
					@endif
				</div>
				
				<div class="_mb15 col-xs-3">
					<button class="_btn _bga _cb _hvra _ml10 _right" type="submit" name="action" value="publish"> 
						{{ trans('address::addresses.settings.update') }}
					</button>
				</div>

			</div>

		</div>

	</form>

</div>
@endsection