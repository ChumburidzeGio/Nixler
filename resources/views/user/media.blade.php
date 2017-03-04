@extends('user.layout')

@section('user_content')
<div class="row">

@foreach($user->getMedia('photo') as $photo)

		<div class="col-md-3 _pb15">
			<div class="_bgw _b1 _brds3 _posr">

				<img src="{{ $photo->photo('user_photos') }}" class="_db _w100">

				<div class="_p5 _brds3 _a6 _bgbt2 _m10 _fs13 _cw _fw600">
						#{{ $photo->pivot->tag }}
				</div>

			</div>
		</div>

	@endforeach

</div>
@endsection