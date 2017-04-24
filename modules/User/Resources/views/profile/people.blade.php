@extends('user::profile.layout')

@section('user_content')
<div class="row">


	@if(count($data))

		@foreach($data as $fl)
		<div class="col-sm-6">
		<a class="_lim _clear _pl0 _bgw _b1 _pl10" href="{{ $fl->link() }}">
		<img src="{{ $fl->avatar('aside') }}" class="_z013 _brds2 _dib _left" height="60" width="60">
				<div class="_pl15 _pr15 _pb10 _oh">
					<span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs17">
					{{ $fl->name }}
					</span>
					<span class="_cbt8 _clear _fs12  _telipsis _w100 _oh _pr10 _oh">{{ '@'.$fl->username }}</span>
				</div>
			</a>
		</div>
		@endforeach

	@else

		<div class="_tac _pt15 _mt70 _c3">
			<h5 class="_fw400">There is no people to show.</h5>
		</div>

	@endif


</div>
@endsection
