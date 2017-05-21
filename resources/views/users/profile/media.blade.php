@extends('users.profile.layout')

@section('user_content')
<div class="row">



	@if(count($data))

		@foreach($data as $photo)

			<div class="col-md-3 _pb15">
				<div class="_bgw _b1 _brds3 _posr">

					@can('update', $user)
					<div class="_p5 _brds3 _a2 _bgbt2 _m10 _fs13 _cw _fw600 _pb0">
							<span ng-click="vm.deletePhoto('{{ $photo->id }}')" confirm-click="@lang('Do you really want to delete photo?')">
		                       <i class="material-icons _fs18">delete</i>
		                    </span>
		                    <form id="{{ $photo->id }}-delete-form" action="{{ url('/media/'.$photo->id) }}" method="POST" class="_d0">
		                    	{{ method_field('DELETE') }}
		                        {{ csrf_field() }}
		                    </form>
					</div>
					@endcan

					<img src="{{ $photo->photo('user_photos') }}" class="_db _w100">

					<div class="_p5 _brds3 _a6 _bgbt2 _m10 _fs13 _cw _fw600">
							#{{ $photo->pivot->tag }}
					</div>

				</div>
			</div>

		@endforeach

	@else

		<div class="_tac _pt15 _mt70 _c3">
			<h5 class="_fw400">@lang('There is no media to show').</h5>
		</div>

	@endif



</div>
@endsection