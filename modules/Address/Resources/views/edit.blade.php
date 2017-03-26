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
				Edit address {{ $address->name }}
			</h1>
		</div>

		<div class="_p15 _pb0">


			<div class="row">
				
				<div class="col-sm-7 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Address Name (Home, Work)" name="name" value="{{ $address->name }}">

					@if ($errors->has('name'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('name') }}</span>
					@endif
				</div>
				
				<div class="col-sm-5 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Postal code" name="post_code" value="{{ $address->post_code }}">

					@if ($errors->has('post_code'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('post_code') }}</span>
					@endif
				</div>
				
				<div class="col-sm-7 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Street address, block, flat" name="street"
					value="{{ $address->street }}">

					@if ($errors->has('street'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('street') }}</span>
					@endif
				</div>

				<div class="col-sm-5 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Phone number" name="phone" value="{{ $address->phone }}">

					@if ($errors->has('phone'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('phone') }}</span>
					@endif
				</div>

				
				<div class="col-sm-12 _mb15">
					<input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="Note" name="note" name="{{ $address->note }}">

					@if ($errors->has('note'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('note') }}</span>
					@endif
				</div>
				
				<div class="_mb15 col-xs-12">
					<button class="_btn _bga _cb _hvra _ml10 _right" type="submit" name="action" value="publish"> 
						Update
					</button>
				</div>

			</div>

		</div>

	</form>

</div>
@endsection